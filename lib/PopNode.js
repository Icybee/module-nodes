define('icybee/nodes/pop-node', [

	'brickrouge',
	'icybee/spinner',
	'icybee/adjust-popover'

], function(Brickrouge, Spinner, AdjustPopover) {

	/**
	 * @property {string} placeholder
	 * @property {string} constructor
	 * @property {string} adjust
	 */
	const OPTIONS_DEFAULT = {

		placeholder: 'Select an entry',
		constructor: 'nodes',
		adjust: 'adjust-node'

	}

	/**
	 * @property bool opening `true` if the popover is being opened. While the property is `true` calls
	 * to `open()` are discarded.
	 */
	return class extends Spinner
	{
		/**
		 * @param {Element} el
		 * @param {OPTIONS_DEFAULT} options
		 */
		constructor(el, options)
		{
			super(el, Object.assign({}, OPTIONS_DEFAULT, options))

			this.opening = false
			this.popover = null

			this.fetchAdjustOperation = new Request.Widget(
				this.options.adjust + '/popup', this.setupAdjust.bind(this)
			)
		}

		open()
		{
			if (this.opening) return

			this.opening = true

			const value = this.value

			this.resetValue = value

			if (this.popover)
			{
				this.popover.adjust.value = value
				this.popover.show()
				this.opening = false

				return
			}

			this.fetchAdjustOperation.get({ selected: value, constructor: this.options.constructor })
		}

		/**
		 * @param {Element} popElement
		 */
		setupAdjust(popElement)
		{
			this.popover = new AdjustPopover(popElement, {

				anchor: this.element

			})

			this.popover.show()
			this.opening = false

			this.popover.adjust.observeChange(this.change.bind(this))
			this.popover.observeAction(this.onAction.bind(this))
		}

		/**
		 * @param {Brickrouge.Popover.ActionEvent} ev
		 *
		 * @listens Brickrouge.Popover#action
		 */
		onAction(ev)
		{
			switch (ev.action)
			{
				case 'cancel':
					this.cancel()
					break
				case 'remove':
					this.remove()
					this.use()
					break
				case 'use':
					this.use()
			}

			this.popover.hide()
		}

		/**
		 * @param {Icybee.Adjust.ChangeEvent} ev
		 */
		change(ev)
		{
			this.value = parseInt(ev.selected.getAttribute('data-nid'))
		}

		cancel()
		{
			this.value = this.resetValue
		}

		remove()
		{
			this.value = 0
		}

		use()
		{
			this.element.fireEvent('change', {})
		}

		reset()
		{

		}
	}

})

/**
 * @param {Brickrouge} Brickrouge
 */
!function (Brickrouge) {

	let Constructor

	Brickrouge.register('PopNode', (element, options) => {

		if (!Constructor)
		{
			Constructor = require('icybee/nodes/pop-node')
		}

		return new Constructor(element, options)

	})

} (Brickrouge)
