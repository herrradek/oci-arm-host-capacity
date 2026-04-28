#!/bin/bash
cd "$(dirname "$0")"

echo "Starting OCI capacity poller — retrying every 60s until instance is created..."
echo "Press Ctrl+C to stop."
echo ""

ATTEMPT=0
while true; do
  ATTEMPT=$((ATTEMPT + 1))
  TIMESTAMP=$(date '+%H:%M:%S')
  echo "[$TIMESTAMP] Attempt $ATTEMPT..."

  OUTPUT=$(php index.php 2>&1)
  echo "$OUTPUT"

  if echo "$OUTPUT" | grep -q '"lifecycleState"'; then
    echo ""
    echo "SUCCESS — instance created!"
    break
  fi

  echo "No capacity yet. Waiting 60s..."
  echo ""
  sleep 60
done
