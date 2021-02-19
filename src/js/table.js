function myFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.querySelector(".myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
	td2 = tr[i].getElementsByTagName("td")[1];
	td3 = tr[i].getElementsByTagName("td")[2];
	td4 = tr[i].getElementsByTagName("td")[3];
	td5 = tr[i].getElementsByTagName("td")[4];
	td6 = tr[i].getElementsByTagName("td")[5];
	td7 = tr[i].getElementsByTagName("td")[6];
	td8 = tr[i].getElementsByTagName("td")[7];
    if (td) {
      txtValue = td.textContent || td.innerText;
	  txtValue2 = td2.textContent || td.innerText;
	  txtValue3 = td3.textContent || td.innerText;
	  txtValue4 = td4.textContent || td.innerText;
	  txtValue5 = td5.textContent || td.innerText;
	  txtValue6 = td6.textContent || td.innerText;
	  txtValue7 = td7.textContent || td.innerText;
      if ((txtValue.toUpperCase().indexOf(filter) > -1)||(txtValue2.toUpperCase().indexOf(filter) > -1)||(txtValue3.toUpperCase().indexOf(filter) > -1)||(txtValue4.toUpperCase().indexOf(filter) > -1)||(txtValue5.toUpperCase().indexOf(filter) > -1)||(txtValue6.toUpperCase().indexOf(filter) > -1)||(txtValue7.toUpperCase().indexOf(filter) > -1)) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    } 
	
  }
}