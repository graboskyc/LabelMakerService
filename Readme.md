# Label Maker Service

## Description 

A c#.net app that runs in a dotnet core Docker container. It dynamically builds a PDF in a format matching Avery 5160 (3 columns x 10 rows) labels to be printed based on data returned from a REST API. 

This is incredibly useful for things like raffle tickets where people can buy many of them and a label must be created and affixed to the ticket for later processing.

## API Format
An HTTP GET webhook is required which returns the following format:

```
[
    {
        "_id":"00024",
        "address":{
            "city":"Cinnaminson",
            "line1":"123 Fake Street",
            "line2":"Apt. 4a",
            "state":"NJ",
            "zip":"08077"
        }, 
        "email":"user@gmail.com",
        "name":"Dennis Smith",
        "qty":5,
        "sku":"SQ5231227"
    },
    ...
```

Thus, for each object it will create `n` number of labels where `n` is the `qty` field. Thus, the above will create 5 labels with the same address and details but labeled `1/5` then `2/5` etc.

## Prerequisites
* Tested in Ubuntu 22.04 LTS
* Install docker cli and docker-compose

## ENV Variables
* `nb` - new build version
* `lblmkraddressbookuri` - the URL of the REST API that returns data in the format listed above
* `lblmkrapipw` - the password you want to provide on this service. You then access this service with this value on the frontend
* `lblmkrctrver` - leave as-is, referencing the `nb` variable. This gets passed into the container so the container knows what version of code it is running

## Building
* Copy the `sample.env` to `.env` 
* Change the `.env` as described above
* Run the `build.sh`

## Running
* After running the "docker-compose build" and "docker-compose up -d", visit `http://localhost:8042`, enter the password and any parameters a PDF should be downloaded
* MAKE SURE YOU HAVE YOUR FIREWALL CONFIGURED IF YOU WANNA ROUTE THIS THROUGH SOMETHING LIKE AN APACHE VIRTUAL HOST TO FORCE HTTPS (otherwise what's the point?)

## Third Party Libraries
* [SharpPDFLabel](https://github.com/finalcut/SharpPDFLabel, there is a forked version for us here: https://github.com/graboskyc/SharpPDFLabel)
* [iTextSharp](https://www.nuget.org/packages/iTextSharp/)
