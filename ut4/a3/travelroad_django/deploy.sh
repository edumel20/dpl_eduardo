#!/bin/bash

ssh dplprod_alumno@10.102.24.40 "
  cd $(dirname $0)
  git pull

  source .venv/bin/activate
  pip install -r requirements.txt

  # python manage.py migrate
  # python manage.py collectstatic --no-input

  supervisorctl restart travelroad
"
