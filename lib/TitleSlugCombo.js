define('icybee/nodes/title-slug-combo', [

], function () {

	return class
	{
		constructor(el, options)
		{
			this.element = el
			this.options = options

			const reminder = el.querySelector('.slug-reminder')
			const target = el.querySelector('.slug')
			const expand = el.querySelector('a[href$="slug-edit"')
			const collapse = el.querySelector('a[href$="slug-collapse"]')
			const del = el.querySelector('a[href$="slug-delete"]')
			const input = target.querySelector('input')

			let toggleState = false

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
				let value = input.get('value')
				const type = value ? 'text' : 'html'

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

			del.addEvent('click', ev => {

				ev.stop()

				input.value = ''
				input.fireEvent('change', {})
			})

			checkInput()
		}

	}

})

!function (Brickrouge) {

	let Constructor

	Brickrouge.register('TitleSlugCombo', function (element, options) {

		if (!Constructor)
		{
			Constructor = require('icybee/nodes/title-slug-combo')
		}

		return new Constructor(element, options)

	})

} (Brickrouge)
