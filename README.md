PHP-Xero PHP Wrapper for CodeIgniter
====================================

Introduction
------------
Port of the PHP-Xero wrapper by [Ronan Quirke](https://github.com/XeroAPI/PHP-Xero) to integrate with CodeIginter framework

A class for interacting with the Xero ([xero.com](http://www.xero.com)) private application API.  It could also be used for the public application API too, but it hasn't been tested with that.  More documentation for Xero can be found at [http://blog.xero.com/developer/api-overview/]()  It is suggested you become familiar with the API before using this class, otherwise it may not make much sense to you - [http://blog.xero.com/developer/api/]()

Thanks for the Oauth* classes provided by Andy Smith, find more about them at [http://oauth.googlecode.com/]().  The
OAuthSignatureMethod_Xero class was written by me, as required by the Oauth classes.  The ArrayToXML classes were sourced from wwwzealdcom's work as shown on the comment dated August 30, 2009 on this page: ([http://snipplr.com/view/3491/convert-php-array-to-xml-or-simple-xml-object-if-you-wish/]())  I made a few minor changes to that code to overcome some bugs.

Requires
--------
PHP5+

Authors
--------
Miguel Guerreiro, Ronan Quirke, Xero (just very minor bugfixes, vast majority of work done by David Pitman)


License
-------
License (applies to Xero and Oauth* classes):
The MIT License

Copyright (c) 2007 Andy Smith (Oauth* classes)
Copyright (c) 2010 David Pitman (Xero class)
Copyright (c) 2012 Ronan Quirke, Xero (Xero class)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

Usage
-----

Basically setup the Xero configuration with your credentials and desired output format in `application/config/xero.php`.
Instantiate the class within you controller using `$this->load->library('xero');`
Then call any of the methods as outlined in the API.  Calling an API method name as a property is the same as calling that API method with no options. Calling the API method as a method with an array as the only input param with like calling the corresponding POST or PUT API method.  You can make more complex GET requests using up to four params on the method.  If you have read the xero api documentation, it should be clear.

### GET Request usage

Retrieving a result set from Xero involves identifying the endpoint you want to access, and optionally, setting some parameters to further filter the result set.
There are 5 possible parameters:

1. Record filter:

    The first parameter could be a boolean "false" or a unique resource identifier: document ID or unique number eg:
    `$this->xero->Invoices('INV-2011', false, false, false, false);`

2. Modified since:

    Second parameter could be a date/time filter to only return data modified since a certain date/time eg:
    `$this->xero->Invoices(false, "2012-05-11T00:00:00");`

3. Custom filters:

    An array of filters, with array keys being filter fields (left of operand), and array values being the right of operand values.
    The array value can be a string or an array(operand, value), or a boolean eg:
    `$this->xero->Invoices(false, false, $filterArray);`

4. Order by:

    Set the ordering of the result set eg:
    `$this->xero->Invoices('', '', '', 'Date', '');`

5. Accept type:

    This only needs to be set if you want to retrieve a PDF version of a document, eg:
    `$this->xero->Invoices($invoice_id, '', '', '', 'pdf');`
		
Further details on filtering GET requests here: http://blog.xero.com/developer/api-overview/http-get/

### Example Setup:
File: application/config/xero.php
(CodeIgniter autoloads these config options if the config file has the same name as the lib file)

```php

    $config = array(
    	'consumer'	=> array(
    		// define your application key
    		'key'		=> '[APPLICATION KEY]',
    		// define your application secret
    		'secret'	=> '[APPLICATION SECRET]'
    	),
    	// set the path to your private and public certificates
    	'certs'		=> array(
    		'private'	=> '/path/to/private-certificate.pem',
    		'public'	=> '/path/to/public-certificate.cer'
    	),
    	// set the format (optional, default to 'xml')
    	'format'	=> 'xml'
    );
```

### Example Usage:
File: application/controllers/test.php

```php
class Test extends CI_Controller {

	public function index() {

		// automatically instantiates the Xero class with your key, secret
		// and paths to your RSA cert and key
		// according to the configuration options you defined in appication/config/xero.php
		$this->load->library('xero');

		// the input format for creating a new contact
		// see http://blog.xero.com/developer/api/contacts/ to understand more
		$new_contact = array(
			array(
				"Name" => "API TEST Contact",
				"FirstName" => "TEST",
				"LastName" => "Contact",
				"Addresses" => array(
					"Address" => array(
						array(
							"AddressType" => "POBOX",
							"AddressLine1" => "PO Box 100",
							"City" => "Someville",
							"PostalCode" => "3890"
						),
						array(
							"AddressType" => "STREET",
							"AddressLine1" => "1 Some Street",
							"City" => "Someville",
							"PostalCode" => "3890"
						)
					)
				)
			)
		);
		// create the contact
		$contact_result = $this->xero->Contacts($new_contact);
		
		// the input format for creating a new invoice (or credit note)
		// see [http://blog.xero.com/developer/api/invoices/]
		$new_invoice = array(
			array(
				"Type"=>"ACCREC",
				"Contact" => array(
					"Name" => "API TEST Contact"
				),
				"Date" => "2010-04-08",
				"DueDate" => "2010-04-30",
				"Status" => "AUTHORISED",
				"LineAmountTypes" => "Exclusive",
				"LineItems"=> array(
					"LineItem" => array(
						array(
							"Description" => "Just another test invoice",
							"Quantity" => "2.0000",
							"UnitAmount" => "250.00",
							"AccountCode" => "200"
						)
					)
				)
			)
		);
		// the input format for creating a new payment
		// see [http://blog.xero.com/developer/api/payments/] to understand more
		$new_payment = array(
			array(
				"Invoice" => array(
					"InvoiceNumber" => "INV-0002"
				),
				"Account" => array(
					"Code" => "[account code]"
				),
				"Date" => "2010-04-09",
				"Amount"=>"100.00",
			)
		);
		
		
		// raise an invoice
		$invoice_result = $this->xero->Invoices($new_invoice);
		
		$payment_result = $this->xero->Payments($new_payment);

		
		// get details of an account, with the name "Test Account"
		$result = $this->xero->Accounts(false, false, array("Name"=>"Test Account"));
		// the params above correspond to the "Optional params for GET Accounts"
		// on http://blog.xero.com/developer/api/accounts/
		
		// to do a POST request, the first and only param must be a
		// multidimensional array as shown above in $new_contact etc.
		
		// get details of all accounts
		$all_accounts = $this->xero->Accounts;
		
		// echo the results back
		if (is_object($result)) {
			// use this to see the source code if the $format option is "xml"
			echo htmlentities($result->asXML()) . "<hr />";
		} else {
			// use this to see the source code if the $format option is "json" or not specified
			echo json_encode($result) . "<hr />";
		}
	}

	public function get_pdf() {
		// first get an invoice number to use
		$org_invoices = $this->xero->Invoices;
		$invoice_count = sizeof($org_invoices->Invoices->Invoice);
		$invoice_index = rand(0,$invoice_count); 
		$invoice_id = (string) $org_invoices->Invoices->Invoice[$invoice_index]->InvoiceID;
		if(!$invoice_id) {
			echo "You will need some invoices for this...";
		}

		// now retrieve that and display the pdf
		$pdf_invoice = $this->xero->Invoices($invoice_id, '', '', '', 'pdf');
		header('Content-type: application/pdf');
		header('Content-Disposition: inline; filename="the.pdf"'); 
		echo ($pdf_invoice);
	}
}
```
