define('icybee/nodes/title-slug-combo', [

], function () {

	return new Class({

		initialize: function(el, options)
		{
			this.element = el

			var reminder = el.querySelector('.slug-reminder')
			, target = el.querySelector('.slug')
			, expand = el.querySelector('a[href$="slug-edit"')
			, collapse = el.querySelector('a[href$="slug-collapse"]')
			, del = el.querySelector('a[href$="slug-delete"]')
			, input = target.querySelector('input')
			, toggleState = false

			function toggle(ev)
			{
				ev.stop()

				toggleState = !toggleState

				target.setStyle('display', toggleState ? 'block' : 'none')
				reminder.setStyle('display', toggleState ? 'none' : 'inline')
				collapse.setStyle('display', toggleState ? 'inline' : 'none')
			}

			expand.addEvent('click', toggle)
			collapse.addEvent('click', toggle)

			function checkInput()
			{
				var value = input.get('value')
				, type = value ? 'text' : 'html'

				if (value)
				{
					value = value.shorten()
					del.getParent('span').setStyle('display', 'inline')
				}
				else
				{
					value = el.get('data-auto-label')
					del.getParent('span').setStyle('display', 'none')
				}

				reminder.querySelector('a').set(type, value)
			}

			input.addEvent('change', checkInput)

			del.addEvent('click', function(ev) {

				ev.stop()

				input.value = ''
				input.fireEvent('change', {})
			})

			checkInput()
		}

	})

})

!function (Brickrouge) {

	var Constructor

	Brickrouge.register('TitleSlugCombo', function (element, options) {

		if (!Constructor)
		{
			Constructor = require('icybee/nodes/title-slug-combo')
		}

		return new Constructor(element, options)

	})

} (Brickrouge)
