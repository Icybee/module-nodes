// TODO-20130605: use hidden control to read/write value

Brickrouge.Widget.AdjustNode = new Class
({
	Implements: [ Options, Events ],

	options:
	{
		adjust: 'adjust-node',
		constructor: 'nodes'
	},

	initialize: function(el, options)
	{
		this.element = document.id(el)
		this.nid = null
		this.fetchResultsOperation = null
		this.setOptions(options)

		var search = this.attachSearch()
		, selected = this.getSelected()

		if (selected)
		{
			this.nid = selected.get('data-nid')
		}

		this.element.addEvent('click:relay(.records [data-nid])', function(ev, el) {

			ev.stop()

			var selected = this.getSelected()

			if (selected)
			{
				selected.getParent('li').removeClass('selected')
			}

			el.getParent('li').addClass('selected')

			this.nid = el.get('data-nid')
			this.fireEvent('select', { target: this, selected: el, event: ev })
			this.fireEvent('change', { target: this, selected: el, event: ev })

		}.bind(this))

		this.element.addEvent('click:relay(.pagination a)', function(ev, el) {

			ev.stop()

			var page = el.get('href').split('#')[1]

			this.fetchResults
			({
				page: page,
				search: search ? search.value : null,
				selected: this.getValue()
			})

		}.bind(this))
	},

	attachSearch: function()
	{
		var search = this.element.getElement('input.search')
		, searchLast = null

		search.onsubmit = function() { return false }

		search.addEvent('keyup', function(ev) {

			if (ev.key == 'esc')
			{
				ev.target.value = ''
			}

			var value = ev.target.value

			if (value != searchLast)
			{
				this.fetchResults({ search: value, selected: this.getValue() })
			}

			searchLast = value

		}.bind(this))

		return search
	},

	fetchResults: function(params)
	{
		if (!this.fetchResultsOperation)
		{
			this.fetchResultsOperation = new Request.Element({

				url: 'widgets/' + this.options.adjust + '/results',

				onSuccess: function(el, response)
				{
					el.replaces(this.element.getElement('.results'))

					Brickrouge.updateDocument(el)

					this.fireEvent('results', { target: this, response: response })
				}
				.bind(this)
			})
		}

		params.constructor = this.options.constructor

		this.fetchResultsOperation.get(params)
	},

	getValue: function()
	{
		return this.nid
	},

	setValue: function(value)
	{
		if (value == this.getValue()) return

		this.nid = value

		this.fetchResults({ selected: value })
	},

	/**
	 * Returns the selected element.
	 *
	 * @returns Element
	 */
	getSelected: function()
	{
		return this.element.getElement('.records li.selected [data-nid]')
	},

	setSelected: function(selected)
	{
		this.setValue(selected)
	}
});/**
 * @property bool opening `true` if the popover is being opened. While the property is `true` calls
 * to `open()` are discarted.
 */
Brickrouge.Widget.PopNode = new Class
({

	Extends: Brickrouge.Widget.Spinner,

	Implements: [ Options, Events ],

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

		this.fetchAdjustOperation = new Request.Widget
		(
			this.options.adjust + '/popup', this.setupAdjust.bind(this)
		)
	},

	open: function()
	{
		if (this.opening) return

		this.opening = true

		var value = this.getValue()

		this.resetValue = value

		if (this.popover)
		{
			this.popover.getAdjust().setValue(value)
			this.popover.show()
			this.opening = false

			return
		}

		this.fetchAdjustOperation.get({ selected: value, constructor: this.options.constructor })
	},

	setupAdjust: function(popElement)
	{
		this.popover = new Icybee.Widget.AdjustPopover(popElement, {

			anchor: this.element

		})

		this.popover.show()
		this.opening = false

		/*
		 * The adjust object is available after the `brickrouge.construct` event has been fired.
		 * The event is fired when the popup is opened.
		 */

		this.popover.adjust.addEvent('change', this.change.bind(this))
		this.popover.addEvent('action', this.onAction.bind(this))
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
		this.setValue(ev.selected.get('data-nid'))
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
});Brickrouge.Widget.TitleSlugCombo = new Class
({
	initialize: function(el, options)
	{
		this.element = el = document.id(el)

		var reminder = el.getElement('.slug-reminder')
		, target = el.getElement('.slug')
		, expand = el.getElement('a[href$="slug-edit"')
		, collapse = el.getElement('a[href$="slug-collapse"]')
		, del = el.getElement('a[href$="slug-delete"]')
		, input = target.getElement('input')
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

			reminder.getElement('a').set(type, value)
		}

		input.addEvent('change', checkInput)

		del.addEvent('click', function(ev) {

			ev.stop()

			input.value = ''
			input.fireEvent('change', {})
		})

		checkInput()
	}
});