jQuery(function($){

	$('#login_open').click(function(){
		$('#form_login').show();
		
		//------------
		// ��������� ���������� ���������� ���������� ���� ��� ��� �� ��������� ������� 
        var form_login_open = 0;
        $('#form_login').hover(function(){
            form_login_open = 1;
        }, function(){
            form_login_open = 0;
        });
        // ����� ������� ����� ���� �� ���� ���� ��������
        $(document).click(function(){
            if(form_login_open == 0){
				$('#form_login').hide();
            }
        });
		//------------
		return false;
	});
})