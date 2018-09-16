$('.generate').on('click', function (e) {
    $('.modal').modal();
})
$('.btn-primary').on('click', function (e) {
    e.preventDefault();
    $('.animationload').css('display','block');
    let value = $('select[name=reportDate]').val();
    $.ajax({
        url:'/generateReport',
        type:'post',
        data:{
            reportDate:value
        },
        success:function (response) {
            if(response.success){
                 window.location.reload()
            }
        }
    })
})