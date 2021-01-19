FROM mcr.microsoft.com/dotnet/aspnet:3.1

LABEL maintainer="chris@grabosky.net"
ENV gskyctrver "notspecified"
ENV gskyaddressbookuri "https://foo.com/api/addressBook"

COPY LabelSite/bin/Release/netcoreapp3.1/ App/
WORKDIR /App
ENTRYPOINT ["dotnet", "LabelSite.dll"]