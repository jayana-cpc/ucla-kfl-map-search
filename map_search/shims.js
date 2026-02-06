(function($){
  // Lightweight multiselect shim if plugin is missing
  if (!$.fn.multiselect) {
    $.fn.multiselect = function(cmd) {
      if (cmd === "getChecked") {
        return this.find('option:selected').toArray();
      }
      return this; // no-op for init
    };
  }
  // Lightweight rangeSlider shim if plugin is missing
  if (!$.fn.rangeSlider) {
    $.fn.rangeSlider = function(arg) {
      if (typeof arg === 'string' && arg === 'values') {
        return {
          min: this.data('rs-min') || 18,
          max: this.data('rs-max') || 80
        };
      }
      if (typeof arg === 'object') {
        var dv = arg.defaultValues || {min:18, max:80};
        this.data('rs-min', dv.min);
        this.data('rs-max', dv.max);
      }
      return this;
    };
  }
})(jQuery);
