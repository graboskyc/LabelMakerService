#!/bin/bash

echo
echo "+======================"
echo "| START: LABELSITE"
echo "+======================"
echo

dos2unix .env
source .env

echo 
echo "LABELSITE: Building webapp"
echo
cd LabelSite
dotnet clean
dotnet build -c Release
cd ..

echo 
echo "LABELSITE: Building container"
echo
docker build -t graboskyc/scbcarnival-labelsite:latest -t graboskyc/scbcarnival-labelsite:v${nb} .

echo 
echo "LABELSITE: Starting container"
echo

docker stop scbls
docker rm scbls
docker run -t -i -d -p 9666:80 --name scbls --restart unless-stopped -e "gskyctrver=${nb}" -e "gskyaddressbookuri=${gskyaddressbookuri}" graboskyc/scbcarnival-labelsite:v${nb}

echo
echo "+======================"
echo "| END: LABELSITE"
echo "+======================"
echo