<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Hitrov\OciApi;
use Hitrov\OciConfig;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$config = new OciConfig(
    $_ENV['OCI_REGION'],
    $_ENV['OCI_USER_ID'],
    $_ENV['OCI_TENANCY_ID'],
    $_ENV['OCI_KEY_FINGERPRINT'],
    $_ENV['OCI_PRIVATE_KEY_FILENAME'],
    $_ENV['OCI_AVAILABILITY_DOMAIN'] ?? '',
    $_ENV['OCI_SUBNET_ID'],
    $_ENV['OCI_IMAGE_ID'],
    (int) $_ENV['OCI_OCPUS'],
    (int) $_ENV['OCI_MEMORY_IN_GBS']
);

$api = new OciApi();
$shape = $_ENV['OCI_SHAPE'];

try {
    $instances = $api->getInstances($config);
    $active = array_filter($instances, function ($i) use ($shape) {
        return $i['shape'] === $shape && $i['lifecycleState'] !== 'TERMINATED';
    });

    if (count($active) > 0) {
        $instance = array_values($active)[0];
        echo json_encode([
            'status' => 'SUCCESS',
            'name'   => $instance['displayName'],
            'state'  => $instance['lifecycleState'],
        ]);
    } else {
        echo json_encode(['status' => 'WAITING']);
    }
} catch (\Exception $e) {
    echo json_encode(['status' => 'ERROR', 'message' => $e->getMessage()]);
    exit(1);
}
