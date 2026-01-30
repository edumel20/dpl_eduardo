#!/bin/bash

ssh dplprod_alumno@10.102.24.40 "
  cd /home/dplprod_alumno/dpl_eduardo/ut4/a3/travelroad_django
  git pull

  source .venv/bin/activate
  pip install -r requirements.txt

  # python manage.py migrate
  # python manage.py collectstatic --no-input

  supervisorctl restart travelroad
"
