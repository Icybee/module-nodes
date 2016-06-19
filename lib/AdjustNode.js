define('icybee/nodes/adjust-node', [

	'brickrouge'

], Brickrouge => {

	"use strict";

	const Subject = Brickrouge.Subject

	/**
	 * @property {string} adjust
	 * @property: {string} constructor
	 */
	const OPTIONS_DEFAULT = {

		adjust: 'adjust-node',
		constructor: 'nodes'

	}

	const SelectEvent = Subject.createEvent(function (target, selected, event) {

		this.target = target
		this.selected = selected
		this.event = event

	})

	const ChangeEvent = Subject.createEvent(function (target, selected, event) {

		this.target = target
		this.selected = selected
		this.event = event

	})

	const ResultEvent = Subject.createEvent(function (target, response) {

		this.target = target
		this.response = response

	})

	const AdjustNode = class extends Brickrouge.mixin(Object, Subject)
	{
		/**
		 * @param {Element} el
		 * @param {OPTIONS_DEFAULT} options
		 */
		constructor(el, options)
		{
			super()

			this.element = el
			this.nid = null
			this.fetchResultsRequest = null
			this.options = Object.assign({}, OPTIONS_DEFAULT, options)

			const selected = this.selected

			if (selected)
			{
				this.value = this.selectedToValue(selected)
			}

			const search = this.attachSearch()

			this.listenToPagination(search)
			this.listenToResultClick()
		}

		listenToResultClick()
		{
			this.element.addDelegatedEventListener('.records [data-nid]', 'click', (ev, el) => {

				ev.stopPropagation()
				ev.preventDefault()

				const selected = this.selected

				if (selected)
				{
					selected.closest('li').classList.remove('selected')
				}

				el.closest('li').classList.add('selected')

				this.nid = el.getAttribute('data-nid')

				this.notify(new SelectEvent(this, el, ev))
				this.notify(new ChangeEvent(this, el, ev))

			})
		}

		/**
		 * @param {Element} search
		 */
		listenToPagination(search)
		{
			this.element.addDelegatedEventListener('.pagination a', 'click', (ev, el) => {

				ev.stopPropagation()
				ev.preventDefault()

				const page = el.getAttribute('href').split('#')[1]

				this.fetchResults({

					page: page,
					search: search ? search.value : null,
					selected: this.value

				})

			})
		}

		/**
		 * @returns {Element|null}
		 */
		attachSearch()
		{
			const search = this.element.querySelector('input.search')

			let searchLast = null

			if (!search) {
				return null
			}

			search.onsubmit = () => false

			/**
			 * @param {KeyboardEvent} ev
			 */
			search.addEventListener('keyup', ev => {

				if (ev.key == 'Escape' || ev.keyCode == 27)
				{
					ev.target.value = ''
				}

				const value = ev.target.value

				if (value != searchLast)
				{
					this.fetchResults({ search: value, selected: this.value })
				}

				searchLast = value

			})

			return search
		}

		/**
		 * @param {{}} params
		 */
		fetchResults(params)
		{
			if (!this.fetchResultsRequest)
			{
				this.fetchResultsRequest = new Request.Element({

					url: `widgets/${this.options.adjust}/results`,

					onSuccess: (el, response) => {

						const target = this.element.querySelector('.results')

						target.parentNode.insertBefore(el, target)
						target.parentNode.removeChild(target)

						this.notify(new ResultEvent(this, response))

					}
				})
			}

			params.constructor = this.options.constructor

			this.fetchResultsRequest.get(params)
		}

		/**
		 * @returns {Number} Node identifier
		 */
		get value()
		{
			return this.nid
		}

		/**
		 * @param {Number} value Node identifier
		 */
		set value(value)
		{
			if (value == this.value) return

			this.nid = value

			this.fetchResults({ selected: value })
		}

		/**
		 * Returns the selected element.
		 *
		 * @returns {Element}
		 */
		get selected()
		{
			return this.element.querySelector('.records li.selected [data-nid]')
		}

		/**
		 * @param {Element} selected
		 */
		set selected(selected)
		{
			this.value = this.selectedToValue(selected)
		}

		/**
		 * @param {Element} selected
		 *
		 * @returns {Number}
		 */
		selectedToValue(selected)
		{
			return parseInt(selected.getAttribute('data-nid'))
		}

		/**
		 * @param {function} callback
		 */
		observeSelect(callback) {

			this.observe(SelectEvent, callback)

		}

		/**
		 * @param {function} callback
		 */
		observeChange(callback) {

			this.observe(ChangeEvent, callback)

		}

		/**
		 * @param {function} callback
		 */
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

/**
 * @param {Brickrouge} Brickrouge
 */
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
