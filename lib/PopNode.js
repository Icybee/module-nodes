define('icybee/nodes/pop-node', [

	'brickrouge',
	'icybee/spinner',
	'icybee/adjust-popover'

], function(Brickrouge, Spinner, AdjustPopover) {

	/**
	 * @property bool opening `true` if the popover is being opened. While the property is `true` calls
	 * to `open()` are discarded.
	 */
	return new Class({

		Extends: Spinner,

		options:
		{
			placeholder: 'Select an entry',
			constructor: 'nodes',
			adjust: 'adjust-node'
		},

		initialize: function(el, options)
		{
			this.parent(el, options)
			this.opening = false
			this.popover = null

			this.fetchAdjustOperation = new Request.Widget(
				this.options.adjust + '/popup', this.setupAdjust.bind(this)
			)
		},

		open: function()
		{
			if (this.opening) return

			this.opening = true

			const value = this.getValue()

			this.resetValue = value

			if (this.popover)
			{
				this.popover.adjust.setValue(value)
				this.popover.show()
				this.opening = false

				return
			}

			this.fetchAdjustOperation.get({ selected: value, constructor: this.options.constructor })
		},

		setupAdjust: function(popElement)
		{
			this.popover = new AdjustPopover(popElement, {

				anchor: this.element

			})

			this.popover.show()
			this.opening = false

			this.popover.adjust.addEvent('change', this.change.bind(this))
			this.popover.observeAction(this.onAction.bind(this))
		},

		onAction: function(ev)
		{
			switch (ev.action)
			{
				case 'cancel':
					this.cancel()
					break
				case 'remove':
					this.remove() // continue
				case 'use':
					this.use()
			}

			this.popover.hide()
		},

		change: function(ev)
		{
			this.setValue(ev.selected.getAttribute('data-nid'))
		},

		cancel: function()
		{
			this.setValue(this.resetValue)
		},

		remove: function()
		{
			this.setValue('')
		},

		use: function()
		{
			this.element.fireEvent('change', {})
		},

		reset: function()
		{

		}
	})

})

!function (Brickrouge) {

	var Constructor

	Brickrouge.register('PopNode', function (element, options) {

		if (!Constructor)
		{
			Constructor = require('icybee/nodes/pop-node')
		}

		return new Constructor(element, options)

	})

} (Brickrouge)
