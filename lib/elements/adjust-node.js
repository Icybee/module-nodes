// TODO-20130605: use hidden control to read/write value

define('icybee/nodes/adjust-node', [], function() {

	return new Class
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
	})

})

require([ 'icybee/nodes/adjust-node' ], function(AdjustNode) {

	Brickrouge.Widget.AdjustNode = AdjustNode

})
;