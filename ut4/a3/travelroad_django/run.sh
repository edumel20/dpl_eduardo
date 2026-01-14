#!/bin/bash

cd /home/dplprod_alumno/dpl_eduardo/ut4/a3/travelroad_django/
source /home/dplprod_alumno/dpl_eduardo/ut4/a3/travelroad_django/.venv/bin/activate
exec gunicorn -b unix:/tmp/travelroad.sock main.wsgi:application
