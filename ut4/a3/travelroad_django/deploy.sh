#!/bin/bash

<<<<<<< HEAD
ssh dplprod_alumno@10.102.24.40 "
  cd $(dirname $0)
  git pull

  source .venv/bin/activate
  pip install -r requirements.txt

  # python manage.py migrate
  # python manage.py collectstatic --no-input

  supervisorctl restart travelroad
=======
ssh dplprod_alumno@10.102.23.40 "
  cd /home/dpl_eduardo/ut4/a3/travelroad_django
  git pull

	source .venv/bin/activate
	supervisorctl restart travelroad
	./manage.py runserver 0.0.0.0:8000
>>>>>>> c22190da29ad0535282689c50b741cf1dff2f17e
"
