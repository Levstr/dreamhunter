jQuery(function($){

	$('#login_open').click(function(){
		$('#form_login').show();
		
		//------------
		// назначаем переменную передающую нахождение мыши вне или за пределами обьекта 
        var form_login_open = 0;
        $('#form_login').hover(function(){
            form_login_open = 1;
        }, function(){
            form_login_open = 0;
        });
        // Ловим событие клика мыши во всем теле страницы
        $(document).click(function(){
            if(form_login_open == 0){
				$('#form_login').hide();
            }
        });
		//------------
		return false;
	});
})