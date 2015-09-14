<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>

	<title>1brood order</title>

	<style type="text/css">

		body
		{
			font-size: 12px;
			width: 400px;
		}

		table.data
		{
			width: 400px;
			font-size: 12px;
		}

		/* Start product info */
		table.productsInfo
		{
			border: 1px solid black;
			width: 100%;
		}
		
		table.productsInfo td
		{
			border-bottom: 1px solid gray;
			padding: 3px;
		}
		
		table.productsInfo *
		{
			line-height: 1;
			padding: 0px;
			margin: 0px;
		}
		
		table.productsInfo tr.prodMain td
		{
			padding-top: 5px;
			padding-left: 5px;
			padding-right: 5px;
			font-weight: bold;
		}
		
		table.productsInfo tr.prodMain.noProdDetail td
		{
			padding-bottom: 5px;
			border-bottom: 1px solid gray;
			
		}
		
		table.productsInfo tr.prodDetail, table.productsInfo tr.prodDetail td
		{
			color: gray;
			padding-bottom: 5px;
			padding-left: 5px;
			padding-right: 5px;
			border-bottom: 1px solid #dddddd;
			border-collapse: collapse;
		}
		
		table.productsInfo tr.last td
		{
			border-bottom: none;
		}
		
		table.productsInfo tr.prodMain td.price
		{
			text-align: center;
		}
		
		
		table.productsInfo tr.lastRow.prodDetail td,
		table.productsInfo tr.lastRow.prodMain td
		{
			border-bottom: none;
		}
		
		td.productName
		{
			font-weight: bold;
		}
		
		/* End products info */

	</style>

	<body>
		<?=$order?>

		<p style="font-size: 10px;"><?=$footer?></p>
	</body>
</html>