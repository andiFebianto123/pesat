$('#select-monthly').on('change', function()
{
    if(this.value=='-'){
        $('#bt-monthly').attr("disabled",'true');
    }else{
        $('#bt-monthly').removeAttr("disabled");
     } 
});
