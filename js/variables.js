var PSAN_C = "#E33258";
var PSAS_C = "#4ECDC4";
var PNAS_C = "#F8CA00";
var NODE_C = "#1565c0";
var EDGE_C = "#b0bec5";
var FILL_C = "#ffffff";
var NODE_SEL_C = "#F8B500";

var render = function(r, n) {
  var color = NODE_C;

  rec = r.rect(n.point[0], n.point[1], 130, 20);
  txt = r.text(n.point[0], n.point[1], (n.label || n.id)).attr({"font-size": "12px", "fill": FILL_C});

  w = txt.getBBox().width + 20;
  h = txt.getBBox().height + 20;
  x = txt.getBBox().x - 10;
  y = txt.getBBox().y - 10;

  attrs = {"title": (n.label || n.id), "fill": color, "stroke": color, r: "2px", "stroke-width": "0px", "width": w, "height": h, "x": x, "y": y};
  rec.attr(attrs);
  var set = r.set().push(rec).push(txt);
  return set;
};

var edgeFactory = function(source, target) {
  var e = jQuery.extend(true, {}, this.template);
  e.source = source;
  e.target = target;
  return e;
}
