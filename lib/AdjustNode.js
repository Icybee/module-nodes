define('icybee/nodes/adjust-node', [

	'brickrouge',
	'icybee/adjust'

],

/**
 *
 * @param {Brickrouge} Brickrouge
 * @param {Icybee.Adjust} Adjust
 *
 * @returns Icybee.Nodes.AdjustNode
 */
(Brickrouge, Adjust) => {

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

	/**
	 * @type {Icybee.Nodes.Adjust.ChangeEvent|Function}
	 */
	const ChangeEvent = Subject.createEvent(function (target, nid, selected, event) {

		this.target = target
		this.value = nid
		this.selected = selected
		this.nid = nid
		this.event = event

	})

	const SelectEvent = Subject.createEvent(function (target, selected, event) {

		this.target = target
		this.selected = selected
		this.event = event

	})

	const LayoutEvent = Subject.createEvent(function () {

	})

	class AdjustNode extends Adjust
	{
		/**
		 * @returns {Icybee.Nodes.Adjust.ChangeEvent}
		 * @constructor
		 */
		static get ChangeEvent()
		{
			return ChangeEvent
		}

		/**
		 * @param {Element} element
		 * @param {OPTIONS_DEFAULT} options
		 */
		constructor(element, options)
		{
			super(element, Object.assign({}, OPTIONS_DEFAULT, options))

			this.nid = null
			this.fetchResultsRequest = null

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

				const nid = this.nid = el.getAttribute('data-nid')

				this.notify(new SelectEvent(this, el, ev))
				this.notify(new ChangeEvent(this, nid, el, ev))

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
						const parent = target.parentNode

						parent.insertBefore(el, target)
						parent.removeChild(target)

						this.notify(new ResultEvent(this, response))
						this.notify(new LayoutEvent)

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
	}

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
