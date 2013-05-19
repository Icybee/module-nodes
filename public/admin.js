document.body.addEvent('click:relay(#manager [data-property="is_online"])', function(ev, target) {

	new Request.API({

		url: manager.destination + '/' + target.value + '/' + (target.checked ? 'online' : 'offline'),

		onFailure: function(response) {

			target.checked = !target.checked

		},

		onSuccess: function(response)
		{
			target.fireEvent('change', {})
		}

	}).post()
})