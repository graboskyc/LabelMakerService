#!/bin/sh

#Messy workaround to build SharpPDFLabel dll on linux systems...
cd /external
#rm -rf SharpPDFLabel
git clone https://github.com/graboskyc/SharpPDFLabel.git
#upgrade-assistant upgrade -t Current SharpPDFLabel/SharpPDFLabel.csproj --non-interactive
cd SharpPDFLabel
git pull
git checkout .
git checkout master
#Cannot compile the Microsoft.VisualStudio tests with mono (and I don't feel like forking to fix it...)
rm -rf SharpPDFLabel.Tests
find . -type f -name *.sln -exec sed -i '/SharpPDFLabel.Tests/d' {} +
dotnet restore
dotnet clean
xbuild SharpPDFLabel.sln
#backup the dlls in-case we can't get/build them later for whatever reason...
cp -p /external/SharpPDFLabel/bin/Debug/itextsharp.dll /libs/.
cp -p /external/SharpPDFLabel/bin/Debug/SharpPDFLabel.dll /libs/.

#build and run the actual application, this runs on port 80 within the container to be re-mapped
cd /app
dotnet restore
dotnet clean
dotnet build -c Release
dotnet /app/LabelSite/bin/Release/net7.0/LabelSite.dll --urls http://0.0.0.0:80
