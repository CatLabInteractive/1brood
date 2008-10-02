function addProductRow (priceOptions)
{
	var tbody = document.getElementById('products').getElementsByTagName ("tbody")[0];

	// First: remove the good old first row
	var noRows = document.getElementById ('noRows');
	if (noRows)
	{
		tbody.removeChild (noRows);
	}

	var rowId = tbody.getElementsByTagName('tr').length;

	// Create row
	var row = document.createElement ("tr")

	// Create td
	var td1 = document.createElement ("td");
	td1.innerHTML = '<input type="text" class="prodName" name="productName'+rowId+'" id="productName'+rowId+'" />';
	
	row.appendChild(td1);

	var td2 = document.createElement ("td");
	td2.innerHTML = '<input type="text" class="prodText" name="productText'+rowId+'" />';
	
	row.appendChild(td2);

	for (var i = 0; i < priceOptions; i ++)
	{
		var td3 = document.createElement ("td");
		td3.innerHTML = '<input type="text" class="prodPrice" name="productPrice'+rowId+'_'+i+'" />';
		row.appendChild(td3);
	}

	var tdLast = document.createElement ('td');
	var sRowOptions = document.getElementById('default_product_categories').innerHTML;
	tdLast.innerHTML = '<select name="categoryId'+rowId+'">'+sRowOptions+'</select>';
	
	row.appendChild(tdLast);
	
	tbody.appendChild(row);
	
	// Focus on the element.
	document.getElementById('productName'+rowId).focus();
 }
