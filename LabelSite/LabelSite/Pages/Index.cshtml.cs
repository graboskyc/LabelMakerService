using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.RazorPages;
using Microsoft.Extensions.Logging;
using SharpPDFLabel;
using SharpPDFLabel.Labels;
using System.Net.Http;
using Newtonsoft.Json.Converters;
using System.Globalization;

namespace LabelSite.Pages
{
    public class IndexModel : PageModel
    {
        private readonly ILogger<IndexModel> _logger;

        public IndexModel(ILogger<IndexModel> logger)
        {
            _logger = logger;
        }

        public FileStreamResult OnGet(string password, int offset=0, string hide="")
        {
            // pdf setup
            Response.Headers.Add("Content-Disposition", "attachment; filename=address_labels.pdf");
            var labelDefinition = new SharpPDFLabel.Labels.A4Labels.Avery.L5160();
            var customLabelCreator = new SharpPDFLabel.CustomLabelCreator(labelDefinition);
            System.IO.Stream pdfStream = null;

            if(password == Environment.GetEnvironmentVariable("gskyapipw")) {
                string[] hideArray = hide.Split(",");

                // uri of the webhook
                // see readme.md for format being returned
                string uri = Environment.GetEnvironmentVariable("gskyaddressbookuri");
                List<Order> orders = null;

                var client = new HttpClient();
                TextInfo myTI = new CultureInfo("en-US", false).TextInfo;

                var result = client.GetAsync(uri).ContinueWith((taskresponse) =>
                {
                    // do an http get
                    var resp = taskresponse.Result;
                    var jsonstring = resp.Content.ReadAsStringAsync();
                    jsonstring.Wait();
                    orders = Newtonsoft.Json.JsonConvert.DeserializeObject<List<Order>>(jsonstring.Result);

                    // put n number of blank labels
                    // useful to reuse a half-used sheet
                    int skip = 0;
                    while(skip < offset) {
                        skip++;
                        var label = new Label(Enums.Alignment.CENTER);
                        customLabelCreator.AddLabel(label);
                    }
                    
                    // set the first label to be details about the job
                    var headerLbl = new Label(Enums.Alignment.LEFT);
                    headerLbl.AddText("Server: " + System.Environment.MachineName,"Verdana", 12, embedFont: true, SharpPDFLabel.Enums.FontStyle.BOLD);
                    headerLbl.AddText("Build: " + Environment.GetEnvironmentVariable("gskyctrver"),"Verdana", 12, embedFont: true);
                    headerLbl.AddText("Reqstr: " + Request.HttpContext.Connection.RemoteIpAddress,"Verdana", 10, embedFont: true);
                    headerLbl.AddText("Printed at:","Verdana", 8, embedFont: true);
                    headerLbl.AddText(DateTime.Now.ToString("o", CultureInfo.CreateSpecificCulture("en-US")),"Verdana", 8, embedFont: true);
                    customLabelCreator.AddLabel(headerLbl);
                    
                    // for each returned object from the API
                    foreach (var o in orders)
                    {
                        // create a label for each qty
                        int i = 1;
                        while (i <= o.qty)
                        {
                            var label = new Label(Enums.Alignment.CENTER);
                            label.AddText(myTI.ToTitleCase(o.name.Trim().ToLower()), "Verdana", 10, embedFont: true, SharpPDFLabel.Enums.FontStyle.BOLD);

                            var productLine = "";
                            if(!hideArray.Contains("OID")) { productLine += "OID: " + o._id + " "; }
                            if(!hideArray.Contains("SKU")) { productLine += "SKU: " + o.sku + " "; }
                            if(!hideArray.Contains("qty")) { productLine += "#" + i.ToString() + "/" + o.qty.ToString(); }
                            label.AddText(productLine, "Verdana", 8, embedFont: true, SharpPDFLabel.Enums.FontStyle.ITALIC);

                            if(!hideArray.Contains("email")) {
                                label.AddText(o.email.ToLower(), "Verdana", 8, embedFont: true, SharpPDFLabel.Enums.FontStyle.ITALIC);
                            }

                            if(!hideArray.Contains("phone")) {
                                label.AddText(o.phone, "Verdana", 8, embedFont: true);
                            }

                            if(!hideArray.Contains("address")) {
                                label.AddText(o.address.line1, "Verdana", 10, embedFont: true);
                                if (o.address.line2.Length > 1)
                                {
                                    label.AddText(o.address.line2, "Verdana", 10, embedFont: true);
                                }
                                label.AddText(o.address.city + ", " + o.address.state + " " + o.address.zip, "Verdana", 10, embedFont: true);
                            }

                            customLabelCreator.AddLabel(label);
                            i++;
                        }
                    }

                    //Create the PDF as a stream
                    pdfStream = customLabelCreator.CreatePDF();                
                });

                result.Wait();

                return new FileStreamResult(pdfStream, "application/pdf");
            } else {
                throw new ArgumentException("Invalid password");
            }

        }
    }

    public class Order
    {
        public string _id { get; set; }
        public int qty { get; set; }
        public string name { get; set; }
        public Address address { get; set; }
        public string sku { get; set; }
        public string email { get; set; }
        public string phone {get; set;}
    }

    public class Address
    {
        public string line1 { get; set; }
        public string line2 { get; set; }
        public string city { get; set; }
        public string state { get; set; }
        public string zip { get; set; }
    }
}
