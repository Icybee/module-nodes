define('icybee/nodes/adjust-node', [

	'brickrouge'

], Brickrouge => {

	"use strict";

	const OPTIONS_DEFAULT = {

		adjust: 'adjust-node',
		constructor: 'nodes'

	}

	const SelectEvent = Brickrouge.Subject.createEvent(function (target, selected, event) {

		this.target = target
		this.selected = selected
		this.event = event

	})

	const ChangeEvent = Brickrouge.Subject.createEvent(function (target, selected, event) {

		this.target = target
		this.selected = selected
		this.event = event

	})

	const ResultEvent = Brickrouge.Subject.createEvent(function (target, response) {

		this.target = target
		this.response = response

	})

	const AdjustNode = class extends Brickrouge.mixin(Object, Brickrouge.Subject)
	{
		constructor(el, options)
		{
			super()

			this.element = el
			this.nid = null
			this.fetchResultsOperation = null
			this.options = Object.assign({}, OPTIONS_DEFAULT, options)

			const search = this.attachSearch()
			const selected = this.getSelected()

			if (selected)
			{
				this.nid = selected.get('data-nid')
			}

			this.element.addDelegatedEventListener('.records [data-nid]', 'click', (ev, el) => {

				ev.stopPropagation()
				ev.preventDefault()

				const selected = this.getSelected()

				if (selected)
				{
					selected.getParent('li').removeClass('selected')
				}

				el.getParent('li').addClass('selected')

				this.nid = el.getAttribute('data-nid')

				this.notify(new SelectEvent(this, el, ev))
				this.notify(new ChangeEvent(this, el, ev))

			})

			this.element.addDelegatedEventListener('.pagination a', 'click', (ev, el) => {

				ev.stopPropagation()
				ev.preventDefault()

				const page = el.getAttribute('href').split('#')[1]

				this.fetchResults({

					page: page,
					search: search ? search.value : null,
					selected: this.getValue()

				})

			})
		}

		attachSearch()
		{
			const search = this.element.querySelector('input.search')

			let searchLast = null

			search.onsubmit = () => false

			search.addEvent('keyup', ev => {

				if (ev.key == 'esc')
				{
					ev.target.value = ''
				}

				const value = ev.target.value

				if (value != searchLast)
				{
					this.fetchResults({ search: value, selected: this.getValue() })
				}

				searchLast = value

			})

			return search
		}

		fetchResults(params)
		{
			if (!this.fetchResultsOperation)
			{
				this.fetchResultsOperation = new Request.Element({

					url: 'widgets/' + this.options.adjust + '/results',

					onSuccess: (el, response) => {

						el.replaces(this.element.querySelector('.results'))

						this.notify(new ResultEvent(this, response))

					}
				})
			}

			params.constructor = this.options.constructor

			this.fetchResultsOperation.get(params)
		}

		getValue()
		{
			return this.nid
		}

		setValue(value)
		{
			if (value == this.getValue()) return

			this.nid = value

			this.fetchResults({ selected: value })
		}

		/**
		 * Returns the selected element.
		 *
		 * @returns {Element}
		 */
		getSelected()
		{
			return this.element.querySelector('.records li.selected [data-nid]')
		}

		setSelected(selected)
		{
			this.setValue(selected)
		}

		observeSelect(callback) {

			this.observe(SelectEvent, callback)

		}

		observeChange(callback) {

			this.observe(ChangeEvent, callback)

		}

		observeResult(callback) {

			this.observe(ResultEvent, callback)

		}
	}

	Object.defineProperties(AdjustNode, {

		EVENT_SELECT: { value: SelectEvent },
		EVENT_CHANGE: { value: ChangeEvent },
		EVENT_RESULT: { value: ResultEvent }

	})

	return AdjustNode

})

!function (Brickrouge) {

	let Constructor

	Brickrouge.register('AdjustNode', (element, options) => {

		if (!Constructor)
		{
			Constructor = require('icybee/nodes/adjust-node')
		}

		return new Constructor(element, options)

	})

} (Brickrouge)
