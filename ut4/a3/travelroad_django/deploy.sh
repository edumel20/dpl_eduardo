#!/bin/bash

ssh dplprod_alumno@10.102.23.40 "
  cd /home/dpl_eduardo/ut4/a3/travelroad_django
  git pull

	source .venv/bin/activate
	supervisorctl restart travelroad
	./manage.py runserver 0.0.0.0:8000
"
