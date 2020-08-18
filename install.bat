@echo off
.\craft install --interactive=0 --username=admin --password=password  --email=admin@temp.local  &&^
.\craft index-assets/all &&^
.\craft migrate/up

