(function($) {

    /**
     * jQuery UI widget for displaying a breakdown donut chart. This
     * widget is based on the d3 library.
     *
     * Dependencies:
     * - jquery.ui.widget
     * - d3
     */
    $.widget('chart.breakdown', {

        /**
         * Default options
         *
         * @property {Object} options
         * @property {Number} options.gpa Grade Point Average to display
         * @property {Array}  options.data Array with data
         * @property {Object} options.colors Array with color values
         */
        options: {
            gpa: 0,
            data: [],
            colors: ["green", "greenyellow", "yellow", "orange", "orangered", "red", "darkred"]
        },
        
        /**
         * Sum of all data values
         *
         * @property {Number} total
         */
        _total: 0,
        
        /**
         * Initializes the widget
         *
         * @private
         */
        _create: function() {
            var win  = $(window),
                opt  = this.options,
                elem = this.element;
                
            this._container = d3.select(elem[0]);
            
            this._total    = d3.sum(this.options.data);
            this._getColor = d3.scale.ordinal().range(opt.colors);
            this._pieData  = this._toPie(opt.data);
            
            this._initElements();
            this._initArcs();
            
            win.on('resize', $.proxy(this._initArcs, this));
        },
        
        /**
         * Initializes the arc functions. Arc functions are necessary to
         * update each time the radius is changed. Will altomaticly trigger
         * #_drawSegments to draw segments with the new arcs.
         *
         * @private
         */
        _initArcs: function() {
            var elem   = this.element,
                radius = Math.min(elem.width(), elem.height()) / 2;
                
            this._getArc = d3.svg.arc()
                .outerRadius(radius - 5)
                .innerRadius(radius - 25);
                            
            this._getArcOver = d3.svg.arc()            
                .outerRadius(radius)
                .innerRadius(radius - 20);
                
            this._drawSegments();
        },
        
        /**
         * Setup the root elements like `svg` and `.text`. Will be called once
         * by creating a new widget
         *
         * @private
         */
        _initElements: function() {
        
            var container = this._container,
                elem      = this.element,
                width     = elem.width(),
                height    = elem.height();
            
            this._textElement = container.append('span')
                .attr('class', 'text')
                .text(this.options.gpa);
                
            this._svgElement = container.append("svg")
                .attr("width", width)
                .attr("height", height)
                .append("g")
                .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

        },
        
        /**
         * Draws the segments. Will create new, delete old and update the
         * already existing.
         *
         * @private
         */
        _drawSegments: function() {
        
            var g, link, path,
                self = this,
                svg  = this._svgElement;
    
            // Apply data        
            g = svg.selectAll(".arc")
                .data(this._pieData);

            // Add new ones
            g.enter().append("g")
                .attr("class", "arc")
                .on("mouseover", $.proxy(this._onSegmentMouseOver, null, this))
                .on("mouseout", $.proxy(this._onSegmentMouseOut, null, this))
                .append('a')
                    .attr('xlink:href', function(d, index) { return '?score=' + index; })
                    .append("path")
                        .style("stroke", "white");    
            
            g.selectAll("path")
                .attr("d", self._getArc)
                .style("fill", function(d, ka, index) { console.info("fill", d); return self._getColor(index); });
                
            // Remove overhead
            g.exit().remove();
        },
        
        /**
         * Callback, called each time the mouse enters a segment. Starts a 
         * transition to zoom the segment and updates the text to a percentage
         * value.
         *
         * @private
         */
        _onSegmentMouseOver: function(self, data) {
        
            var text = self._toPercentage(data.value/self._total)
                           .replace(/\.0+%$/, "%");
                           
            d3.select(this).select('path').transition()
               .duration(350)
               .attr("d", self._getArcOver);
               
           self._textElement.text(text);
        },
        
        /**
         * Callback, called each time the mouse leaves a segment. Starts a
         * transtision back to the initial state and puts the gpa back to
         * the text element.
         *
         * @private
         */
        _onSegmentMouseOut: function(self, data) {
        
            d3.select(this).select('path').transition()
               .duration(350)
               .attr("d", self._getArc);
               
           self._textElement.text(self.options.gpa);
        },
        
        /**
         * Converts a simple integer to a pie layout object
         *
         * @see https://github.com/mbostock/d3/wiki/Pie-Layout
         * @param {Array} data array
         * @private
         */
        _toPie: d3.layout.pie().sort(null).value(function(d) { return d; }),
        
        /**
         * Converts float number to percentage value
         *
         * @private
         */
        _toPercentage: d3.format('.1%'),
        
        /**
         * Overrides the default `setOption()` method of the jquery ui widget
         * factory. In this case special handling for setting colors, and data
         * is implemented to inititalize all the helper methods.
         *
         * @private
         */
        _setOption: function( key, value ) {
            switch( key ) {
                case "colors":
                    this._getColor = d3.scale.ordinal().range(value);
                    this._drawSegments();
                    break;
                case "data":
                    this._total   = d3.sum(value);
                    this._pieData = this._toPie(value);
                    this._drawSegments();
                    break;
                case "gpa":
                    this._textElement.text(value)
                    break;
            }
            $.Widget.prototype._setOption.apply( this, arguments );
        },
    });

})(jQuery);