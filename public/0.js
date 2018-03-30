webpackJsonp([0],{

/***/ 46:
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;!(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__, !(function webpackMissingModule() { var e = new Error("Cannot find module \"./base\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/shape/Polyline\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../util/shape/Icon\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../util/shape/HalfSmoothPolygon\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../component/axis\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../component/grid\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../component/dataZoom\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../config\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../util/ecData\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/tool/util\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/tool/color\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../chart\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())], __WEBPACK_AMD_DEFINE_RESULT__ = (function (e) {
  function t(e, t, i, a, o) {
    n.call(this, e, t, i, a, o), this.refresh(a);
  }function i(e, t, i) {
    var n = t.x,
        a = t.y,
        r = t.width,
        s = t.height,
        l = s / 2;t.symbol.match("empty") && (e.fillStyle = "#fff"), t.brushType = "both";var h = t.symbol.replace("empty", "").toLowerCase();h.match("star") ? (l = h.replace("star", "") - 0 || 5, a -= 1, h = "star") : ("rectangle" === h || "arrow" === h) && (n += (r - s) / 2, r = s);var d = "";if (h.match("image") && (d = h.replace(new RegExp("^image:\\/\\/"), ""), h = "image", n += Math.round((r - s) / 2) - 1, r = s += 2), h = o.prototype.iconLibrary[h]) {
      var c = t.x,
          m = t.y;e.moveTo(c, m + l), e.lineTo(c + 5, m + l), e.moveTo(c + t.width - 5, m + l), e.lineTo(c + t.width, m + l);var p = this;h(e, { x: n + 4, y: a + 4, width: r - 8, height: s - 8, n: l, image: d }, function () {
        p.modSelf(), i();
      });
    } else e.moveTo(n, a + l), e.lineTo(n + r, a + l);
  }var n = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"./base\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())),
      a = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/shape/Polyline\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())),
      o = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../util/shape/Icon\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())),
      r = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../util/shape/HalfSmoothPolygon\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));__webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../component/axis\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())), __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../component/grid\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())), __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../component/dataZoom\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));var s = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../config\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));s.line = { zlevel: 0, z: 2, clickable: !0, legendHoverLink: !0, xAxisIndex: 0, yAxisIndex: 0, dataFilter: "nearest", itemStyle: { normal: { label: { show: !1 }, lineStyle: { width: 2, type: "solid", shadowColor: "rgba(0,0,0,0)", shadowBlur: 0, shadowOffsetX: 0, shadowOffsetY: 0 } }, emphasis: { label: { show: !1 } } }, symbolSize: 2, showAllSymbol: !1 };var l = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../util/ecData\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())),
      h = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/tool/util\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())),
      d = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/tool/color\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));return t.prototype = { type: s.CHART_TYPE_LINE, _buildShape: function _buildShape() {
      this.finalPLMap = {}, this._buildPosition();
    }, _buildHorizontal: function _buildHorizontal(e, t, i, n) {
      for (var a, o, r, s, l, h, d, c, m, p = this.series, u = i[0][0], V = p[u], U = this.component.xAxis.getAxis(V.xAxisIndex || 0), g = {}, f = 0, y = t; y > f && null != U.getNameByIndex(f); f++) {
        o = U.getCoordByIndex(f);for (var b = 0, _ = i.length; _ > b; b++) {
          a = this.component.yAxis.getAxis(p[i[b][0]].yAxisIndex || 0), l = s = d = h = a.getCoord(0);for (var x = 0, k = i[b].length; k > x; x++) {
            u = i[b][x], V = p[u], c = V.data[f], m = this.getDataFromOption(c, "-"), g[u] = g[u] || [], n[u] = n[u] || { min: Number.POSITIVE_INFINITY, max: Number.NEGATIVE_INFINITY, sum: 0, counter: 0, average: 0 }, "-" !== m ? (m >= 0 ? (s -= x > 0 ? a.getCoordSize(m) : l - a.getCoord(m), r = s) : 0 > m && (h += x > 0 ? a.getCoordSize(m) : a.getCoord(m) - d, r = h), g[u].push([o, r, f, U.getNameByIndex(f), o, l]), n[u].min > m && (n[u].min = m, n[u].minY = r, n[u].minX = o), n[u].max < m && (n[u].max = m, n[u].maxY = r, n[u].maxX = o), n[u].sum += m, n[u].counter++) : g[u].length > 0 && (this.finalPLMap[u] = this.finalPLMap[u] || [], this.finalPLMap[u].push(g[u]), g[u] = []);
          }
        }s = this.component.grid.getY();for (var v, b = 0, _ = i.length; _ > b; b++) {
          for (var x = 0, k = i[b].length; k > x; x++) {
            u = i[b][x], V = p[u], c = V.data[f], m = this.getDataFromOption(c, "-"), "-" == m && this.deepQuery([c, V, this.option], "calculable") && (v = this.deepQuery([c, V], "symbolSize"), s += 2 * v + 5, r = s, this.shapeList.push(this._getCalculableItem(u, f, U.getNameByIndex(f), o, r, "horizontal")));
          }
        }
      }for (var L in g) {
        g[L].length > 0 && (this.finalPLMap[L] = this.finalPLMap[L] || [], this.finalPLMap[L].push(g[L]), g[L] = []);
      }this._calculMarkMapXY(n, i, "y"), this._buildBorkenLine(e, this.finalPLMap, U, "horizontal");
    }, _buildVertical: function _buildVertical(e, t, i, n) {
      for (var a, o, r, s, l, h, d, c, m, p = this.series, u = i[0][0], V = p[u], U = this.component.yAxis.getAxis(V.yAxisIndex || 0), g = {}, f = 0, y = t; y > f && null != U.getNameByIndex(f); f++) {
        r = U.getCoordByIndex(f);for (var b = 0, _ = i.length; _ > b; b++) {
          a = this.component.xAxis.getAxis(p[i[b][0]].xAxisIndex || 0), l = s = d = h = a.getCoord(0);for (var x = 0, k = i[b].length; k > x; x++) {
            u = i[b][x], V = p[u], c = V.data[f], m = this.getDataFromOption(c, "-"), g[u] = g[u] || [], n[u] = n[u] || { min: Number.POSITIVE_INFINITY, max: Number.NEGATIVE_INFINITY, sum: 0, counter: 0, average: 0 }, "-" !== m ? (m >= 0 ? (s += x > 0 ? a.getCoordSize(m) : a.getCoord(m) - l, o = s) : 0 > m && (h -= x > 0 ? a.getCoordSize(m) : d - a.getCoord(m), o = h), g[u].push([o, r, f, U.getNameByIndex(f), l, r]), n[u].min > m && (n[u].min = m, n[u].minX = o, n[u].minY = r), n[u].max < m && (n[u].max = m, n[u].maxX = o, n[u].maxY = r), n[u].sum += m, n[u].counter++) : g[u].length > 0 && (this.finalPLMap[u] = this.finalPLMap[u] || [], this.finalPLMap[u].push(g[u]), g[u] = []);
          }
        }s = this.component.grid.getXend();for (var v, b = 0, _ = i.length; _ > b; b++) {
          for (var x = 0, k = i[b].length; k > x; x++) {
            u = i[b][x], V = p[u], c = V.data[f], m = this.getDataFromOption(c, "-"), "-" == m && this.deepQuery([c, V, this.option], "calculable") && (v = this.deepQuery([c, V], "symbolSize"), s -= 2 * v + 5, o = s, this.shapeList.push(this._getCalculableItem(u, f, U.getNameByIndex(f), o, r, "vertical")));
          }
        }
      }for (var L in g) {
        g[L].length > 0 && (this.finalPLMap[L] = this.finalPLMap[L] || [], this.finalPLMap[L].push(g[L]), g[L] = []);
      }this._calculMarkMapXY(n, i, "x"), this._buildBorkenLine(e, this.finalPLMap, U, "vertical");
    }, _buildOther: function _buildOther(e, t, i, n) {
      for (var a, o = this.series, r = {}, s = 0, l = i.length; l > s; s++) {
        for (var h = 0, d = i[s].length; d > h; h++) {
          var c = i[s][h],
              m = o[c];a = this.component.xAxis.getAxis(m.xAxisIndex || 0);var p = this.component.yAxis.getAxis(m.yAxisIndex || 0),
              u = p.getCoord(0);r[c] = r[c] || [], n[c] = n[c] || { min0: Number.POSITIVE_INFINITY, min1: Number.POSITIVE_INFINITY, max0: Number.NEGATIVE_INFINITY, max1: Number.NEGATIVE_INFINITY, sum0: 0, sum1: 0, counter0: 0, counter1: 0, average0: 0, average1: 0 };for (var V = 0, U = m.data.length; U > V; V++) {
            var g = m.data[V],
                f = this.getDataFromOption(g, "-");if (f instanceof Array) {
              var y = a.getCoord(f[0]),
                  b = p.getCoord(f[1]);r[c].push([y, b, V, f[0], y, u]), n[c].min0 > f[0] && (n[c].min0 = f[0], n[c].minY0 = b, n[c].minX0 = y), n[c].max0 < f[0] && (n[c].max0 = f[0], n[c].maxY0 = b, n[c].maxX0 = y), n[c].sum0 += f[0], n[c].counter0++, n[c].min1 > f[1] && (n[c].min1 = f[1], n[c].minY1 = b, n[c].minX1 = y), n[c].max1 < f[1] && (n[c].max1 = f[1], n[c].maxY1 = b, n[c].maxX1 = y), n[c].sum1 += f[1], n[c].counter1++;
            }
          }
        }
      }for (var _ in r) {
        r[_].length > 0 && (this.finalPLMap[_] = this.finalPLMap[_] || [], this.finalPLMap[_].push(r[_]), r[_] = []);
      }this._calculMarkMapXY(n, i, "xy"), this._buildBorkenLine(e, this.finalPLMap, a, "other");
    }, _buildBorkenLine: function _buildBorkenLine(e, t, i, n) {
      for (var o, s = "other" == n ? "horizontal" : n, c = this.series, m = e.length - 1; m >= 0; m--) {
        var p = e[m],
            u = c[p],
            V = t[p];if (u.type === this.type && null != V) for (var U = this._getBbox(p, s), g = this._sIndex2ColorMap[p], f = this.query(u, "itemStyle.normal.lineStyle.width"), y = this.query(u, "itemStyle.normal.lineStyle.type"), b = this.query(u, "itemStyle.normal.lineStyle.color"), _ = this.getItemStyleColor(this.query(u, "itemStyle.normal.color"), p, -1), x = null != this.query(u, "itemStyle.normal.areaStyle"), k = this.query(u, "itemStyle.normal.areaStyle.color"), v = 0, L = V.length; L > v; v++) {
          var w = V[v],
              W = "other" != n && this._isLarge(s, w);if (W) w = this._getLargePointList(s, w, u.dataFilter);else for (var X = 0, I = w.length; I > X; X++) {
            o = u.data[w[X][2]], (this.deepQuery([o, u, this.option], "calculable") || this.deepQuery([o, u], "showAllSymbol") || "categoryAxis" === i.type && i.isMainAxis(w[X][2]) && "none" != this.deepQuery([o, u], "symbol")) && this.shapeList.push(this._getSymbol(p, w[X][2], w[X][3], w[X][0], w[X][1], s));
          }var S = new a({ zlevel: u.zlevel, z: u.z, style: { miterLimit: f, pointList: w, strokeColor: b || _ || g, lineWidth: f, lineType: y, smooth: this._getSmooth(u.smooth), smoothConstraint: U, shadowColor: this.query(u, "itemStyle.normal.lineStyle.shadowColor"), shadowBlur: this.query(u, "itemStyle.normal.lineStyle.shadowBlur"), shadowOffsetX: this.query(u, "itemStyle.normal.lineStyle.shadowOffsetX"), shadowOffsetY: this.query(u, "itemStyle.normal.lineStyle.shadowOffsetY") }, hoverable: !1, _main: !0, _seriesIndex: p, _orient: s });if (l.pack(S, c[p], p, 0, v, c[p].name), this.shapeList.push(S), x) {
            var K = new r({ zlevel: u.zlevel, z: u.z, style: { miterLimit: f, pointList: h.clone(w).concat([[w[w.length - 1][4], w[w.length - 1][5]], [w[0][4], w[0][5]]]), brushType: "fill", smooth: this._getSmooth(u.smooth), smoothConstraint: U, color: k ? k : d.alpha(g, .5) }, highlightStyle: { brushType: "fill" }, hoverable: !1, _main: !0, _seriesIndex: p, _orient: s });l.pack(K, c[p], p, 0, v, c[p].name), this.shapeList.push(K);
          }
        }
      }
    }, _getBbox: function _getBbox(e, t) {
      var i = this.component.grid.getBbox(),
          n = this.xMarkMap[e];return null != n.minX0 ? [[Math.min(n.minX0, n.maxX0, n.minX1, n.maxX1), Math.min(n.minY0, n.maxY0, n.minY1, n.maxY1)], [Math.max(n.minX0, n.maxX0, n.minX1, n.maxX1), Math.max(n.minY0, n.maxY0, n.minY1, n.maxY1)]] : ("horizontal" === t ? (i[0][1] = Math.min(n.minY, n.maxY), i[1][1] = Math.max(n.minY, n.maxY)) : (i[0][0] = Math.min(n.minX, n.maxX), i[1][0] = Math.max(n.minX, n.maxX)), i);
    }, _isLarge: function _isLarge(e, t) {
      return t.length < 2 ? !1 : "horizontal" === e ? Math.abs(t[0][0] - t[1][0]) < .5 : Math.abs(t[0][1] - t[1][1]) < .5;
    }, _getLargePointList: function _getLargePointList(e, t, i) {
      var n;n = "horizontal" === e ? this.component.grid.getWidth() : this.component.grid.getHeight();var a = t.length,
          o = [];if ("function" != typeof i) switch (i) {case "min":
          i = function i(e) {
            return Math.max.apply(null, e);
          };break;case "max":
          i = function i(e) {
            return Math.min.apply(null, e);
          };break;case "average":
          i = function i(e) {
            for (var t = 0, i = 0; i < e.length; i++) {
              t += e[i];
            }return t / e.length;
          };break;default:
          i = function i(e) {
            return e[0];
          };}for (var r = [], s = 0; n > s; s++) {
        var l = Math.floor(a / n * s),
            h = Math.min(Math.floor(a / n * (s + 1)), a);if (!(l >= h)) {
          for (var d = l; h > d; d++) {
            r[d - l] = "horizontal" === e ? t[d][1] : t[d][0];
          }r.length = h - l;for (var c = i(r), m = -1, p = 1 / 0, d = l; h > d; d++) {
            var u = "horizontal" === e ? t[d][1] : t[d][0],
                V = Math.abs(u - c);p > V && (m = d, p = V);
          }var U = t[m].slice();"horizontal" === e ? U[1] = c : U[0] = c, o.push(U);
        }
      }return o;
    }, _getSmooth: function _getSmooth(e) {
      return e ? .3 : 0;
    }, _getCalculableItem: function _getCalculableItem(e, t, i, n, a, o) {
      var r = this.series,
          l = r[e].calculableHolderColor || this.ecTheme.calculableHolderColor || s.calculableHolderColor,
          h = this._getSymbol(e, t, i, n, a, o);return h.style.color = l, h.style.strokeColor = l, h.rotation = [0, 0], h.hoverable = !1, h.draggable = !1, h.style.text = void 0, h;
    }, _getSymbol: function _getSymbol(e, t, i, n, a, o) {
      var r = this.series,
          s = r[e],
          l = s.data[t],
          h = this.getSymbolShape(s, e, l, t, i, n, a, this._sIndex2ShapeMap[e], this._sIndex2ColorMap[e], "#fff", "vertical" === o ? "horizontal" : "vertical");return h.zlevel = s.zlevel, h.z = s.z + 1, this.deepQuery([l, s, this.option], "calculable") && (this.setCalculable(h), h.draggable = !0), h;
    }, getMarkCoord: function getMarkCoord(e, t) {
      var i = this.series[e],
          n = this.xMarkMap[e],
          a = this.component.xAxis.getAxis(i.xAxisIndex),
          o = this.component.yAxis.getAxis(i.yAxisIndex);if (t.type && ("max" === t.type || "min" === t.type || "average" === t.type)) {
        var r = null != t.valueIndex ? t.valueIndex : null != n.maxX0 ? "1" : "";return [n[t.type + "X" + r], n[t.type + "Y" + r], n[t.type + "Line" + r], n[t.type + r]];
      }return ["string" != typeof t.xAxis && a.getCoordByIndex ? a.getCoordByIndex(t.xAxis || 0) : a.getCoord(t.xAxis || 0), "string" != typeof t.yAxis && o.getCoordByIndex ? o.getCoordByIndex(t.yAxis || 0) : o.getCoord(t.yAxis || 0)];
    }, refresh: function refresh(e) {
      e && (this.option = e, this.series = e.series), this.backupShapeList(), this._buildShape();
    }, ontooltipHover: function ontooltipHover(e, t) {
      for (var i, n, a = e.seriesIndex, o = e.dataIndex, r = a.length; r--;) {
        if (i = this.finalPLMap[a[r]]) for (var s = 0, l = i.length; l > s; s++) {
          n = i[s];for (var h = 0, d = n.length; d > h; h++) {
            o === n[h][2] && t.push(this._getSymbol(a[r], n[h][2], n[h][3], n[h][0], n[h][1], "horizontal"));
          }
        }
      }
    }, addDataAnimation: function addDataAnimation(e, t) {
      function i() {
        V--, 0 === V && t && t();
      }function n(e) {
        e.style.controlPointList = null;
      }for (var a = this.series, o = {}, r = 0, s = e.length; s > r; r++) {
        o[e[r][0]] = e[r];
      }for (var l, h, d, c, m, p, u, V = 0, r = this.shapeList.length - 1; r >= 0; r--) {
        if (m = this.shapeList[r]._seriesIndex, o[m] && !o[m][3]) {
          if (this.shapeList[r]._main && this.shapeList[r].style.pointList.length > 1) {
            if (p = this.shapeList[r].style.pointList, h = Math.abs(p[0][0] - p[1][0]), c = Math.abs(p[0][1] - p[1][1]), u = "horizontal" === this.shapeList[r]._orient, o[m][2]) {
              if ("half-smooth-polygon" === this.shapeList[r].type) {
                var U = p.length;this.shapeList[r].style.pointList[U - 3] = p[U - 2], this.shapeList[r].style.pointList[U - 3][u ? 0 : 1] = p[U - 4][u ? 0 : 1], this.shapeList[r].style.pointList[U - 2] = p[U - 1];
              }this.shapeList[r].style.pointList.pop(), u ? (l = h, d = 0) : (l = 0, d = -c);
            } else {
              if (this.shapeList[r].style.pointList.shift(), "half-smooth-polygon" === this.shapeList[r].type) {
                var g = this.shapeList[r].style.pointList.pop();u ? g[0] = p[0][0] : g[1] = p[0][1], this.shapeList[r].style.pointList.push(g);
              }u ? (l = -h, d = 0) : (l = 0, d = c);
            }this.shapeList[r].style.controlPointList = null, this.zr.modShape(this.shapeList[r]);
          } else {
            if (o[m][2] && this.shapeList[r]._dataIndex === a[m].data.length - 1) {
              this.zr.delShape(this.shapeList[r].id);continue;
            }if (!o[m][2] && 0 === this.shapeList[r]._dataIndex) {
              this.zr.delShape(this.shapeList[r].id);continue;
            }
          }this.shapeList[r].position = [0, 0], V++, this.zr.animate(this.shapeList[r].id, "").when(this.query(this.option, "animationDurationUpdate"), { position: [l, d] }).during(n).done(i).start();
        }
      }V || t && t();
    } }, o.prototype.iconLibrary.legendLineIcon = i, h.inherits(t, n), __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../chart\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())).define("line", t), t;
}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)), !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__, !(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/shape/Base\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/shape/util/smoothBezier\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/tool/util\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/shape/Polygon\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())], __WEBPACK_AMD_DEFINE_RESULT__ = (function (e) {
  function t(e) {
    i.call(this, e);
  }var i = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/shape/Base\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())),
      n = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/shape/util/smoothBezier\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())),
      a = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/tool/util\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));return t.prototype = { type: "half-smooth-polygon", buildPath: function buildPath(t, i) {
      var a = i.pointList;if (!(a.length < 2)) if (i.smooth) {
        var o = n(a.slice(0, -2), i.smooth, !1, i.smoothConstraint);t.moveTo(a[0][0], a[0][1]);for (var r, s, l, h = a.length, d = 0; h - 3 > d; d++) {
          r = o[2 * d], s = o[2 * d + 1], l = a[d + 1], t.bezierCurveTo(r[0], r[1], s[0], s[1], l[0], l[1]);
        }t.lineTo(a[h - 2][0], a[h - 2][1]), t.lineTo(a[h - 1][0], a[h - 1][1]), t.lineTo(a[0][0], a[0][1]);
      } else __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/shape/Polygon\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())).prototype.buildPath(t, i);
    } }, a.inherits(t, i), t;
}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));

/***/ }),

/***/ 47:
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;!(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__, !(function webpackMissingModule() { var e = new Error("Cannot find module \"./base\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../util/shape/Symbol\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../component/axis\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../component/grid\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../component/dataZoom\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../component/dataRange\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../config\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/tool/util\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/tool/color\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../chart\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())], __WEBPACK_AMD_DEFINE_RESULT__ = (function (e) {
  function t(e, t, n, a, o) {
    i.call(this, e, t, n, a, o), this.refresh(a);
  }var i = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"./base\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())),
      n = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../util/shape/Symbol\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));__webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../component/axis\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())), __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../component/grid\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())), __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../component/dataZoom\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())), __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../component/dataRange\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));var a = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../config\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));a.scatter = { zlevel: 0, z: 2, clickable: !0, legendHoverLink: !0, xAxisIndex: 0, yAxisIndex: 0, symbolSize: 4, large: !1, largeThreshold: 2e3, itemStyle: { normal: { label: { show: !1 } }, emphasis: { label: { show: !1 } } } };var o = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/tool/util\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())),
      r = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/tool/color\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));return t.prototype = { type: a.CHART_TYPE_SCATTER, _buildShape: function _buildShape() {
      var e = this.series;this._sIndex2ColorMap = {}, this._symbol = this.option.symbolList, this._sIndex2ShapeMap = {}, this.selectedMap = {}, this.xMarkMap = {};for (var t, i, n, o, s = this.component.legend, l = [], h = 0, d = e.length; d > h; h++) {
        if (t = e[h], i = t.name, t.type === a.CHART_TYPE_SCATTER) {
          if (e[h] = this.reformOption(e[h]), this.legendHoverLink = e[h].legendHoverLink || this.legendHoverLink, this._sIndex2ShapeMap[h] = this.query(t, "symbol") || this._symbol[h % this._symbol.length], s) {
            if (this.selectedMap[i] = s.isSelected(i), this._sIndex2ColorMap[h] = r.alpha(s.getColor(i), .5), n = s.getItemShape(i)) {
              var o = this._sIndex2ShapeMap[h];n.style.brushType = o.match("empty") ? "stroke" : "both", o = o.replace("empty", "").toLowerCase(), o.match("rectangle") && (n.style.x += Math.round((n.style.width - n.style.height) / 2), n.style.width = n.style.height), o.match("star") && (n.style.n = o.replace("star", "") - 0 || 5, o = "star"), o.match("image") && (n.style.image = o.replace(new RegExp("^image:\\/\\/"), ""), n.style.x += Math.round((n.style.width - n.style.height) / 2), n.style.width = n.style.height, o = "image"), n.style.iconType = o, s.setItemShape(i, n);
            }
          } else this.selectedMap[i] = !0, this._sIndex2ColorMap[h] = r.alpha(this.zr.getColor(h), .5);this.selectedMap[i] && l.push(h);
        }
      }this._buildSeries(l), this.addShapeList();
    }, _buildSeries: function _buildSeries(e) {
      if (0 !== e.length) {
        for (var t, i, n, a, o, r, s, l, h = this.series, d = {}, c = 0, m = e.length; m > c; c++) {
          if (t = e[c], i = h[t], 0 !== i.data.length) {
            o = this.component.xAxis.getAxis(i.xAxisIndex || 0), r = this.component.yAxis.getAxis(i.yAxisIndex || 0), d[t] = [];for (var p = 0, u = i.data.length; u > p; p++) {
              n = i.data[p], a = this.getDataFromOption(n, "-"), "-" === a || a.length < 2 || (s = o.getCoord(a[0]), l = r.getCoord(a[1]), d[t].push([s, l, p, n.name || ""]));
            }this.xMarkMap[t] = this._markMap(o, r, i.data, d[t]), this.buildMark(t);
          }
        }this._buildPointList(d);
      }
    }, _markMap: function _markMap(e, t, i, n) {
      for (var a, o = { min0: Number.POSITIVE_INFINITY, max0: Number.NEGATIVE_INFINITY, sum0: 0, counter0: 0, average0: 0, min1: Number.POSITIVE_INFINITY, max1: Number.NEGATIVE_INFINITY, sum1: 0, counter1: 0, average1: 0 }, r = 0, s = n.length; s > r; r++) {
        a = i[n[r][2]].value || i[n[r][2]], o.min0 > a[0] && (o.min0 = a[0], o.minY0 = n[r][1], o.minX0 = n[r][0]), o.max0 < a[0] && (o.max0 = a[0], o.maxY0 = n[r][1], o.maxX0 = n[r][0]), o.sum0 += a[0], o.counter0++, o.min1 > a[1] && (o.min1 = a[1], o.minY1 = n[r][1], o.minX1 = n[r][0]), o.max1 < a[1] && (o.max1 = a[1], o.maxY1 = n[r][1], o.maxX1 = n[r][0]), o.sum1 += a[1], o.counter1++;
      }var l = this.component.grid.getX(),
          h = this.component.grid.getXend(),
          d = this.component.grid.getY(),
          c = this.component.grid.getYend();o.average0 = o.sum0 / o.counter0;var m = e.getCoord(o.average0);o.averageLine0 = [[m, c], [m, d]], o.minLine0 = [[o.minX0, c], [o.minX0, d]], o.maxLine0 = [[o.maxX0, c], [o.maxX0, d]], o.average1 = o.sum1 / o.counter1;var p = t.getCoord(o.average1);return o.averageLine1 = [[l, p], [h, p]], o.minLine1 = [[l, o.minY1], [h, o.minY1]], o.maxLine1 = [[l, o.maxY1], [h, o.maxY1]], o;
    }, _buildPointList: function _buildPointList(e) {
      var t,
          i,
          n,
          a,
          o = this.series;for (var r in e) {
        if (t = o[r], i = e[r], t.large && t.data.length > t.largeThreshold) this.shapeList.push(this._getLargeSymbol(t, i, this.getItemStyleColor(this.query(t, "itemStyle.normal.color"), r, -1) || this._sIndex2ColorMap[r]));else for (var s = 0, l = i.length; l > s; s++) {
          n = i[s], a = this._getSymbol(r, n[2], n[3], n[0], n[1]), a && this.shapeList.push(a);
        }
      }
    }, _getSymbol: function _getSymbol(e, t, i, n, a) {
      var o,
          r = this.series,
          s = r[e],
          l = s.data[t],
          h = this.component.dataRange;if (h) {
        if (o = isNaN(l[2]) ? this._sIndex2ColorMap[e] : h.getColor(l[2]), !o) return null;
      } else o = this._sIndex2ColorMap[e];var d = this.getSymbolShape(s, e, l, t, i, n, a, this._sIndex2ShapeMap[e], o, "rgba(0,0,0,0)", "vertical");return d.zlevel = s.zlevel, d.z = s.z, d._main = !0, d;
    }, _getLargeSymbol: function _getLargeSymbol(e, t, i) {
      return new n({ zlevel: e.zlevel, z: e.z, _main: !0, hoverable: !1, style: { pointList: t, color: i, strokeColor: i }, highlightStyle: { pointList: [] } });
    }, getMarkCoord: function getMarkCoord(e, t) {
      var i,
          n = this.series[e],
          a = this.xMarkMap[e],
          o = this.component.xAxis.getAxis(n.xAxisIndex),
          r = this.component.yAxis.getAxis(n.yAxisIndex);if (!t.type || "max" !== t.type && "min" !== t.type && "average" !== t.type) i = ["string" != typeof t.xAxis && o.getCoordByIndex ? o.getCoordByIndex(t.xAxis || 0) : o.getCoord(t.xAxis || 0), "string" != typeof t.yAxis && r.getCoordByIndex ? r.getCoordByIndex(t.yAxis || 0) : r.getCoord(t.yAxis || 0)];else {
        var s = null != t.valueIndex ? t.valueIndex : 1;i = [a[t.type + "X" + s], a[t.type + "Y" + s], a[t.type + "Line" + s], a[t.type + s]];
      }return i;
    }, refresh: function refresh(e) {
      e && (this.option = e, this.series = e.series), this.backupShapeList(), this._buildShape();
    }, ondataRange: function ondataRange(e, t) {
      this.component.dataRange && (this.refresh(), t.needRefresh = !0);
    } }, o.inherits(t, i), __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../chart\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())).define("scatter", t), t;
}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)), !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__, !(function webpackMissingModule() { var e = new Error("Cannot find module \"./base\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/shape/Text\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/shape/Rectangle\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../util/shape/HandlePolygon\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../config\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/tool/util\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/tool/event\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/tool/area\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/tool/color\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../component\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())], __WEBPACK_AMD_DEFINE_RESULT__ = (function (e) {
  function t(e, t, n, a, o) {
    i.call(this, e, t, n, a, o);var s = this;s._ondrift = function (e, t) {
      return s.__ondrift(this, e, t);
    }, s._ondragend = function () {
      return s.__ondragend();
    }, s._dataRangeSelected = function (e) {
      return s.__dataRangeSelected(e);
    }, s._dispatchHoverLink = function (e) {
      return s.__dispatchHoverLink(e);
    }, s._onhoverlink = function (e) {
      return s.__onhoverlink(e);
    }, this._selectedMap = {}, this._range = {}, this.refresh(a), t.bind(r.EVENT.HOVER, this._onhoverlink);
  }var i = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"./base\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())),
      n = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/shape/Text\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())),
      a = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/shape/Rectangle\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())),
      o = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../util/shape/HandlePolygon\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())),
      r = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../config\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));r.dataRange = { zlevel: 0, z: 4, show: !0, orient: "vertical", x: "left", y: "bottom", backgroundColor: "rgba(0,0,0,0)", borderColor: "#ccc", borderWidth: 0, padding: 5, itemGap: 10, itemWidth: 20, itemHeight: 14, precision: 0, splitNumber: 5, splitList: null, calculable: !1, selectedMode: !0, hoverLink: !0, realtime: !0, color: ["#006edd", "#e0ffff"], textStyle: { color: "#333" } };var s = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/tool/util\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())),
      l = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/tool/event\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())),
      h = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/tool/area\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())),
      d = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/tool/color\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));return t.prototype = { type: r.COMPONENT_TYPE_DATARANGE, _textGap: 10, _buildShape: function _buildShape() {
      if (this._itemGroupLocation = this._getItemGroupLocation(), this._buildBackground(), this._isContinuity() ? this._buildGradient() : this._buildItem(), this.dataRangeOption.show) for (var e = 0, t = this.shapeList.length; t > e; e++) {
        this.zr.addShape(this.shapeList[e]);
      }this._syncShapeFromRange();
    }, _buildItem: function _buildItem() {
      var e,
          t,
          i,
          o,
          r = this._valueTextList,
          s = r.length,
          l = this.getFont(this.dataRangeOption.textStyle),
          d = this._itemGroupLocation.x,
          c = this._itemGroupLocation.y,
          m = this.dataRangeOption.itemWidth,
          p = this.dataRangeOption.itemHeight,
          u = this.dataRangeOption.itemGap,
          g = h.getTextHeight("国", l);"vertical" == this.dataRangeOption.orient && "right" == this.dataRangeOption.x && (d = this._itemGroupLocation.x + this._itemGroupLocation.width - m);var V = !0;this.dataRangeOption.text && (V = !1, this.dataRangeOption.text[0] && (i = this._getTextShape(d, c, this.dataRangeOption.text[0]), "horizontal" == this.dataRangeOption.orient ? d += h.getTextWidth(this.dataRangeOption.text[0], l) + this._textGap : (c += g + this._textGap, i.style.y += g / 2 + this._textGap, i.style.textBaseline = "bottom"), this.shapeList.push(new n(i))));for (var U = 0; s > U; U++) {
        e = r[U], o = this.getColorByIndex(U), t = this._getItemShape(d, c, m, p, this._selectedMap[U] ? o : "#ccc"), t._idx = U, t.onmousemove = this._dispatchHoverLink, this.dataRangeOption.selectedMode && (t.clickable = !0, t.onclick = this._dataRangeSelected), this.shapeList.push(new a(t)), V && (i = { zlevel: this.getZlevelBase(), z: this.getZBase(), style: { x: d + m + 5, y: c, color: this._selectedMap[U] ? this.dataRangeOption.textStyle.color : "#ccc", text: r[U], textFont: l, textBaseline: "top" }, highlightStyle: { brushType: "fill" } }, "vertical" == this.dataRangeOption.orient && "right" == this.dataRangeOption.x && (i.style.x -= m + 10, i.style.textAlign = "right"), i._idx = U, i.onmousemove = this._dispatchHoverLink, this.dataRangeOption.selectedMode && (i.clickable = !0, i.onclick = this._dataRangeSelected), this.shapeList.push(new n(i))), "horizontal" == this.dataRangeOption.orient ? d += m + (V ? 5 : 0) + (V ? h.getTextWidth(e, l) : 0) + u : c += p + u;
      }!V && this.dataRangeOption.text[1] && ("horizontal" == this.dataRangeOption.orient ? d = d - u + this._textGap : c = c - u + this._textGap, i = this._getTextShape(d, c, this.dataRangeOption.text[1]), "horizontal" != this.dataRangeOption.orient && (i.style.y -= 5, i.style.textBaseline = "top"), this.shapeList.push(new n(i)));
    }, _buildGradient: function _buildGradient() {
      var t,
          i,
          o = this.getFont(this.dataRangeOption.textStyle),
          r = this._itemGroupLocation.x,
          s = this._itemGroupLocation.y,
          l = this.dataRangeOption.itemWidth,
          d = this.dataRangeOption.itemHeight,
          c = h.getTextHeight("国", o),
          m = 10,
          p = !0;this.dataRangeOption.text && (p = !1, this.dataRangeOption.text[0] && (i = this._getTextShape(r, s, this.dataRangeOption.text[0]), "horizontal" == this.dataRangeOption.orient ? r += h.getTextWidth(this.dataRangeOption.text[0], o) + this._textGap : (s += c + this._textGap, i.style.y += c / 2 + this._textGap, i.style.textBaseline = "bottom"), this.shapeList.push(new n(i))));for (var u = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/tool/color\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())), g = 1 / (this.dataRangeOption.color.length - 1), V = [], U = 0, f = this.dataRangeOption.color.length; f > U; U++) {
        V.push([U * g, this.dataRangeOption.color[U]]);
      }"horizontal" == this.dataRangeOption.orient ? (t = { zlevel: this.getZlevelBase(), z: this.getZBase(), style: { x: r, y: s, width: l * m, height: d, color: u.getLinearGradient(r, s, r + l * m, s, V) }, hoverable: !1 }, r += l * m + this._textGap) : (t = { zlevel: this.getZlevelBase(), z: this.getZBase(), style: { x: r, y: s, width: l, height: d * m, color: u.getLinearGradient(r, s, r, s + d * m, V) }, hoverable: !1 }, s += d * m + this._textGap), this.shapeList.push(new a(t)), this._calculableLocation = t.style, this.dataRangeOption.calculable && (this._buildFiller(), this._bulidMask(), this._bulidHandle()), this._buildIndicator(), !p && this.dataRangeOption.text[1] && (i = this._getTextShape(r, s, this.dataRangeOption.text[1]), this.shapeList.push(new n(i)));
    }, _buildIndicator: function _buildIndicator() {
      var e,
          t,
          i = this._calculableLocation.x,
          n = this._calculableLocation.y,
          a = this._calculableLocation.width,
          r = this._calculableLocation.height,
          s = 5;"horizontal" == this.dataRangeOption.orient ? "bottom" != this.dataRangeOption.y ? (e = [[i, n + r], [i - s, n + r + s], [i + s, n + r + s]], t = "bottom") : (e = [[i, n], [i - s, n - s], [i + s, n - s]], t = "top") : "right" != this.dataRangeOption.x ? (e = [[i + a, n], [i + a + s, n - s], [i + a + s, n + s]], t = "right") : (e = [[i, n], [i - s, n - s], [i - s, n + s]], t = "left"), this._indicatorShape = { style: { pointList: e, color: "#fff", __rect: { x: Math.min(e[0][0], e[1][0]), y: Math.min(e[0][1], e[1][1]), width: s * ("horizontal" == this.dataRangeOption.orient ? 2 : 1), height: s * ("horizontal" == this.dataRangeOption.orient ? 1 : 2) } }, highlightStyle: { brushType: "fill", textPosition: t, textColor: this.dataRangeOption.textStyle.color }, hoverable: !1 }, this._indicatorShape = new o(this._indicatorShape);
    }, _buildFiller: function _buildFiller() {
      this._fillerShape = { zlevel: this.getZlevelBase(), z: this.getZBase() + 1, style: { x: this._calculableLocation.x, y: this._calculableLocation.y, width: this._calculableLocation.width, height: this._calculableLocation.height, color: "rgba(255,255,255,0)" }, highlightStyle: { strokeColor: "rgba(255,255,255,0.5)", lineWidth: 1 }, draggable: !0, ondrift: this._ondrift, ondragend: this._ondragend, onmousemove: this._dispatchHoverLink, _type: "filler" }, this._fillerShape = new a(this._fillerShape), this.shapeList.push(this._fillerShape);
    }, _bulidHandle: function _bulidHandle() {
      var e,
          t,
          i,
          n,
          a,
          r,
          s,
          l,
          d = this._calculableLocation.x,
          c = this._calculableLocation.y,
          m = this._calculableLocation.width,
          p = this._calculableLocation.height,
          u = this.getFont(this.dataRangeOption.textStyle),
          g = h.getTextHeight("国", u),
          V = Math.max(h.getTextWidth(this._textFormat(this.dataRangeOption.max), u), h.getTextWidth(this._textFormat(this.dataRangeOption.min), u)) + 2;"horizontal" == this.dataRangeOption.orient ? "bottom" != this.dataRangeOption.y ? (e = [[d, c], [d, c + p + g], [d - g, c + p + g], [d - 1, c + p], [d - 1, c]], t = d - V / 2 - g, i = c + p + g / 2 + 2, n = { x: d - V - g, y: c + p, width: V + g, height: g }, a = [[d + m, c], [d + m, c + p + g], [d + m + g, c + p + g], [d + m + 1, c + p], [d + m + 1, c]], r = d + m + V / 2 + g, s = i, l = { x: d + m, y: c + p, width: V + g, height: g }) : (e = [[d, c + p], [d, c - g], [d - g, c - g], [d - 1, c], [d - 1, c + p]], t = d - V / 2 - g, i = c - g / 2 - 2, n = { x: d - V - g, y: c - g, width: V + g, height: g }, a = [[d + m, c + p], [d + m, c - g], [d + m + g, c - g], [d + m + 1, c], [d + m + 1, c + p]], r = d + m + V / 2 + g, s = i, l = { x: d + m, y: c - g, width: V + g, height: g }) : (V += g, "right" != this.dataRangeOption.x ? (e = [[d, c], [d + m + g, c], [d + m + g, c - g], [d + m, c - 1], [d, c - 1]], t = d + m + V / 2 + g / 2, i = c - g / 2, n = { x: d + m, y: c - g, width: V + g, height: g }, a = [[d, c + p], [d + m + g, c + p], [d + m + g, c + g + p], [d + m, c + 1 + p], [d, c + p + 1]], r = t, s = c + p + g / 2, l = { x: d + m, y: c + p, width: V + g, height: g }) : (e = [[d + m, c], [d - g, c], [d - g, c - g], [d, c - 1], [d + m, c - 1]], t = d - V / 2 - g / 2, i = c - g / 2, n = { x: d - V - g, y: c - g, width: V + g, height: g }, a = [[d + m, c + p], [d - g, c + p], [d - g, c + g + p], [d, c + 1 + p], [d + m, c + p + 1]], r = t, s = c + p + g / 2, l = { x: d - V - g, y: c + p, width: V + g, height: g })), this._startShape = { style: { pointList: e, text: this._textFormat(this.dataRangeOption.max), textX: t, textY: i, textFont: u, color: this.getColor(this.dataRangeOption.max), rect: n, x: e[0][0], y: e[0][1], _x: e[0][0], _y: e[0][1] } }, this._startShape.highlightStyle = { strokeColor: this._startShape.style.color, lineWidth: 1 }, this._endShape = { style: { pointList: a, text: this._textFormat(this.dataRangeOption.min), textX: r, textY: s, textFont: u, color: this.getColor(this.dataRangeOption.min), rect: l, x: a[0][0], y: a[0][1], _x: a[0][0], _y: a[0][1] } }, this._endShape.highlightStyle = { strokeColor: this._endShape.style.color, lineWidth: 1 }, this._startShape.zlevel = this._endShape.zlevel = this.getZlevelBase(), this._startShape.z = this._endShape.z = this.getZBase() + 1, this._startShape.draggable = this._endShape.draggable = !0, this._startShape.ondrift = this._endShape.ondrift = this._ondrift, this._startShape.ondragend = this._endShape.ondragend = this._ondragend, this._startShape.style.textColor = this._endShape.style.textColor = this.dataRangeOption.textStyle.color, this._startShape.style.textAlign = this._endShape.style.textAlign = "center", this._startShape.style.textPosition = this._endShape.style.textPosition = "specific", this._startShape.style.textBaseline = this._endShape.style.textBaseline = "middle", this._startShape.style.width = this._endShape.style.width = 0, this._startShape.style.height = this._endShape.style.height = 0, this._startShape.style.textPosition = this._endShape.style.textPosition = "specific", this._startShape = new o(this._startShape), this._endShape = new o(this._endShape), this.shapeList.push(this._startShape), this.shapeList.push(this._endShape);
    }, _bulidMask: function _bulidMask() {
      var e = this._calculableLocation.x,
          t = this._calculableLocation.y,
          i = this._calculableLocation.width,
          n = this._calculableLocation.height;this._startMask = { zlevel: this.getZlevelBase(), z: this.getZBase() + 1, style: { x: e, y: t, width: "horizontal" == this.dataRangeOption.orient ? 0 : i, height: "horizontal" == this.dataRangeOption.orient ? n : 0, color: "#ccc" }, hoverable: !1 }, this._endMask = { zlevel: this.getZlevelBase(), z: this.getZBase() + 1, style: { x: "horizontal" == this.dataRangeOption.orient ? e + i : e, y: "horizontal" == this.dataRangeOption.orient ? t : t + n, width: "horizontal" == this.dataRangeOption.orient ? 0 : i, height: "horizontal" == this.dataRangeOption.orient ? n : 0, color: "#ccc" }, hoverable: !1 }, this._startMask = new a(this._startMask), this._endMask = new a(this._endMask), this.shapeList.push(this._startMask), this.shapeList.push(this._endMask);
    }, _buildBackground: function _buildBackground() {
      var e = this.reformCssArray(this.dataRangeOption.padding);this.shapeList.push(new a({ zlevel: this.getZlevelBase(), z: this.getZBase(), hoverable: !1, style: { x: this._itemGroupLocation.x - e[3], y: this._itemGroupLocation.y - e[0], width: this._itemGroupLocation.width + e[3] + e[1], height: this._itemGroupLocation.height + e[0] + e[2], brushType: 0 === this.dataRangeOption.borderWidth ? "fill" : "both", color: this.dataRangeOption.backgroundColor, strokeColor: this.dataRangeOption.borderColor, lineWidth: this.dataRangeOption.borderWidth } }));
    }, _getItemGroupLocation: function _getItemGroupLocation() {
      var e = this._valueTextList,
          t = e.length,
          i = this.dataRangeOption.itemGap,
          n = this.dataRangeOption.itemWidth,
          a = this.dataRangeOption.itemHeight,
          o = 0,
          r = 0,
          s = this.getFont(this.dataRangeOption.textStyle),
          l = h.getTextHeight("国", s),
          d = 10;if ("horizontal" == this.dataRangeOption.orient) {
        if (this.dataRangeOption.text || this._isContinuity()) o = (this._isContinuity() ? n * d + i : t * (n + i)) + (this.dataRangeOption.text && "undefined" != typeof this.dataRangeOption.text[0] ? h.getTextWidth(this.dataRangeOption.text[0], s) + this._textGap : 0) + (this.dataRangeOption.text && "undefined" != typeof this.dataRangeOption.text[1] ? h.getTextWidth(this.dataRangeOption.text[1], s) + this._textGap : 0);else {
          n += 5;for (var c = 0; t > c; c++) {
            o += n + h.getTextWidth(e[c], s) + i;
          }
        }o -= i, r = Math.max(l, a);
      } else {
        var m;if (this.dataRangeOption.text || this._isContinuity()) r = (this._isContinuity() ? a * d + i : t * (a + i)) + (this.dataRangeOption.text && "undefined" != typeof this.dataRangeOption.text[0] ? this._textGap + l : 0) + (this.dataRangeOption.text && "undefined" != typeof this.dataRangeOption.text[1] ? this._textGap + l : 0), m = Math.max(h.getTextWidth(this.dataRangeOption.text && this.dataRangeOption.text[0] || "", s), h.getTextWidth(this.dataRangeOption.text && this.dataRangeOption.text[1] || "", s)), o = Math.max(n, m);else {
          r = (a + i) * t, n += 5, m = 0;for (var c = 0; t > c; c++) {
            m = Math.max(m, h.getTextWidth(e[c], s));
          }o = n + m;
        }r -= i;
      }var p,
          u = this.reformCssArray(this.dataRangeOption.padding),
          g = this.zr.getWidth();switch (this.dataRangeOption.x) {case "center":
          p = Math.floor((g - o) / 2);break;case "left":
          p = u[3] + this.dataRangeOption.borderWidth;break;case "right":
          p = g - o - u[1] - this.dataRangeOption.borderWidth;break;default:
          p = this.parsePercent(this.dataRangeOption.x, g), p = isNaN(p) ? 0 : p;}var V,
          U = this.zr.getHeight();switch (this.dataRangeOption.y) {case "top":
          V = u[0] + this.dataRangeOption.borderWidth;break;case "bottom":
          V = U - r - u[2] - this.dataRangeOption.borderWidth;break;case "center":
          V = Math.floor((U - r) / 2);break;default:
          V = this.parsePercent(this.dataRangeOption.y, U), V = isNaN(V) ? 0 : V;}if (this.dataRangeOption.calculable) {
        var f = Math.max(h.getTextWidth(this.dataRangeOption.max, s), h.getTextWidth(this.dataRangeOption.min, s)) + l;"horizontal" == this.dataRangeOption.orient ? (f > p && (p = f), p + o + f > g && (p -= f)) : (l > V && (V = l), V + r + l > U && (V -= l));
      }return { x: p, y: V, width: o, height: r };
    }, _getTextShape: function _getTextShape(e, t, i) {
      return { zlevel: this.getZlevelBase(), z: this.getZBase(), style: { x: "horizontal" == this.dataRangeOption.orient ? e : this._itemGroupLocation.x + this._itemGroupLocation.width / 2, y: "horizontal" == this.dataRangeOption.orient ? this._itemGroupLocation.y + this._itemGroupLocation.height / 2 : t, color: this.dataRangeOption.textStyle.color, text: i, textFont: this.getFont(this.dataRangeOption.textStyle), textBaseline: "horizontal" == this.dataRangeOption.orient ? "middle" : "top", textAlign: "horizontal" == this.dataRangeOption.orient ? "left" : "center" }, hoverable: !1 };
    }, _getItemShape: function _getItemShape(e, t, i, n, a) {
      return { zlevel: this.getZlevelBase(), z: this.getZBase(), style: { x: e, y: t + 1, width: i, height: n - 2, color: a }, highlightStyle: { strokeColor: a, lineWidth: 1 } };
    }, __ondrift: function __ondrift(e, t, i) {
      var n = this._calculableLocation.x,
          a = this._calculableLocation.y,
          o = this._calculableLocation.width,
          r = this._calculableLocation.height;return "horizontal" == this.dataRangeOption.orient ? e.style.x + t <= n ? e.style.x = n : e.style.x + t + e.style.width >= n + o ? e.style.x = n + o - e.style.width : e.style.x += t : e.style.y + i <= a ? e.style.y = a : e.style.y + i + e.style.height >= a + r ? e.style.y = a + r - e.style.height : e.style.y += i, "filler" == e._type ? this._syncHandleShape() : this._syncFillerShape(e), this.dataRangeOption.realtime && this._dispatchDataRange(), !0;
    }, __ondragend: function __ondragend() {
      this.isDragend = !0;
    }, ondragend: function ondragend(e, t) {
      this.isDragend && e.target && (t.dragOut = !0, t.dragIn = !0, this.dataRangeOption.realtime || this._dispatchDataRange(), t.needRefresh = !1, this.isDragend = !1);
    }, _syncShapeFromRange: function _syncShapeFromRange() {
      var e = this.dataRangeOption.range || {},
          t = e.start,
          i = e.end;if (t > i && (t = [i, i = t][0]), this._range.end = null != t ? t : null != this._range.end ? this._range.end : 0, this._range.start = null != i ? i : null != this._range.start ? this._range.start : 100, 100 != this._range.start || 0 !== this._range.end) {
        if ("horizontal" == this.dataRangeOption.orient) {
          var n = this._fillerShape.style.width;this._fillerShape.style.x += n * (100 - this._range.start) / 100, this._fillerShape.style.width = n * (this._range.start - this._range.end) / 100;
        } else {
          var a = this._fillerShape.style.height;this._fillerShape.style.y += a * (100 - this._range.start) / 100, this._fillerShape.style.height = a * (this._range.start - this._range.end) / 100;
        }this.zr.modShape(this._fillerShape.id), this._syncHandleShape();
      }
    }, _syncHandleShape: function _syncHandleShape() {
      var e = this._calculableLocation.x,
          t = this._calculableLocation.y,
          i = this._calculableLocation.width,
          n = this._calculableLocation.height;"horizontal" == this.dataRangeOption.orient ? (this._startShape.style.x = this._fillerShape.style.x, this._startMask.style.width = this._startShape.style.x - e, this._endShape.style.x = this._fillerShape.style.x + this._fillerShape.style.width, this._endMask.style.x = this._endShape.style.x, this._endMask.style.width = e + i - this._endShape.style.x, this._range.start = Math.ceil(100 - (this._startShape.style.x - e) / i * 100), this._range.end = Math.floor(100 - (this._endShape.style.x - e) / i * 100)) : (this._startShape.style.y = this._fillerShape.style.y, this._startMask.style.height = this._startShape.style.y - t, this._endShape.style.y = this._fillerShape.style.y + this._fillerShape.style.height, this._endMask.style.y = this._endShape.style.y, this._endMask.style.height = t + n - this._endShape.style.y, this._range.start = Math.ceil(100 - (this._startShape.style.y - t) / n * 100), this._range.end = Math.floor(100 - (this._endShape.style.y - t) / n * 100)), this._syncShape();
    }, _syncFillerShape: function _syncFillerShape(e) {
      var t,
          i,
          n = this._calculableLocation.x,
          a = this._calculableLocation.y,
          o = this._calculableLocation.width,
          r = this._calculableLocation.height;"horizontal" == this.dataRangeOption.orient ? (t = this._startShape.style.x, i = this._endShape.style.x, e.id == this._startShape.id && t >= i ? (i = t, this._endShape.style.x = t) : e.id == this._endShape.id && t >= i && (t = i, this._startShape.style.x = t), this._fillerShape.style.x = t, this._fillerShape.style.width = i - t, this._startMask.style.width = t - n, this._endMask.style.x = i, this._endMask.style.width = n + o - i, this._range.start = Math.ceil(100 - (t - n) / o * 100), this._range.end = Math.floor(100 - (i - n) / o * 100)) : (t = this._startShape.style.y, i = this._endShape.style.y, e.id == this._startShape.id && t >= i ? (i = t, this._endShape.style.y = t) : e.id == this._endShape.id && t >= i && (t = i, this._startShape.style.y = t), this._fillerShape.style.y = t, this._fillerShape.style.height = i - t, this._startMask.style.height = t - a, this._endMask.style.y = i, this._endMask.style.height = a + r - i, this._range.start = Math.ceil(100 - (t - a) / r * 100), this._range.end = Math.floor(100 - (i - a) / r * 100)), this._syncShape();
    }, _syncShape: function _syncShape() {
      this._startShape.position = [this._startShape.style.x - this._startShape.style._x, this._startShape.style.y - this._startShape.style._y], this._startShape.style.text = this._textFormat(this._gap * this._range.start + this.dataRangeOption.min), this._startShape.style.color = this._startShape.highlightStyle.strokeColor = this.getColor(this._gap * this._range.start + this.dataRangeOption.min), this._endShape.position = [this._endShape.style.x - this._endShape.style._x, this._endShape.style.y - this._endShape.style._y], this._endShape.style.text = this._textFormat(this._gap * this._range.end + this.dataRangeOption.min), this._endShape.style.color = this._endShape.highlightStyle.strokeColor = this.getColor(this._gap * this._range.end + this.dataRangeOption.min), this.zr.modShape(this._startShape.id), this.zr.modShape(this._endShape.id), this.zr.modShape(this._startMask.id), this.zr.modShape(this._endMask.id), this.zr.modShape(this._fillerShape.id), this.zr.refreshNextFrame();
    }, _dispatchDataRange: function _dispatchDataRange() {
      this.messageCenter.dispatch(r.EVENT.DATA_RANGE, null, { range: { start: this._range.end, end: this._range.start } }, this.myChart);
    }, __dataRangeSelected: function __dataRangeSelected(e) {
      if ("single" === this.dataRangeOption.selectedMode) for (var t in this._selectedMap) {
        this._selectedMap[t] = !1;
      }var i = e.target._idx;this._selectedMap[i] = !this._selectedMap[i];var n, a;this._useCustomizedSplit() ? (n = this._splitList[i].max, a = this._splitList[i].min) : (n = (this._colorList.length - i) * this._gap + this.dataRangeOption.min, a = n - this._gap), this.messageCenter.dispatch(r.EVENT.DATA_RANGE_SELECTED, e.event, { selected: this._selectedMap, target: i, valueMax: n, valueMin: a }, this.myChart), this.messageCenter.dispatch(r.EVENT.REFRESH, null, null, this.myChart);
    }, __dispatchHoverLink: function __dispatchHoverLink(e) {
      var t, i;if (this.dataRangeOption.calculable) {
        var n,
            a = this.dataRangeOption.max - this.dataRangeOption.min;n = "horizontal" == this.dataRangeOption.orient ? (1 - (l.getX(e.event) - this._calculableLocation.x) / this._calculableLocation.width) * a : (1 - (l.getY(e.event) - this._calculableLocation.y) / this._calculableLocation.height) * a, t = n - .05 * a, i = n + .05 * a;
      } else if (this._useCustomizedSplit()) {
        var o = e.target._idx;i = this._splitList[o].max, t = this._splitList[o].min;
      } else {
        var o = e.target._idx;i = (this._colorList.length - o) * this._gap + this.dataRangeOption.min, t = i - this._gap;
      }this.messageCenter.dispatch(r.EVENT.DATA_RANGE_HOVERLINK, e.event, { valueMin: t, valueMax: i }, this.myChart);
    }, __onhoverlink: function __onhoverlink(e) {
      if (this.dataRangeOption.show && this.dataRangeOption.hoverLink && this._indicatorShape && e && null != e.seriesIndex && null != e.dataIndex) {
        var t = e.value;if ("" === t || isNaN(t)) return;t < this.dataRangeOption.min ? t = this.dataRangeOption.min : t > this.dataRangeOption.max && (t = this.dataRangeOption.max), this._indicatorShape.position = "horizontal" == this.dataRangeOption.orient ? [(this.dataRangeOption.max - t) / (this.dataRangeOption.max - this.dataRangeOption.min) * this._calculableLocation.width, 0] : [0, (this.dataRangeOption.max - t) / (this.dataRangeOption.max - this.dataRangeOption.min) * this._calculableLocation.height], this._indicatorShape.style.text = this._textFormat(e.value), this._indicatorShape.style.color = this.getColor(t), this.zr.addHoverShape(this._indicatorShape);
      }
    }, _textFormat: function _textFormat(e, t) {
      var i = this.dataRangeOption;if (e !== -Number.MAX_VALUE && (e = (+e).toFixed(i.precision)), null != t && t !== Number.MAX_VALUE && (t = (+t).toFixed(i.precision)), i.formatter) {
        if ("string" == typeof i.formatter) return i.formatter.replace("{value}", e === -Number.MAX_VALUE ? "min" : e).replace("{value2}", t === Number.MAX_VALUE ? "max" : t);if ("function" == typeof i.formatter) return i.formatter.call(this.myChart, e, t);
      }return null == t ? e : e === -Number.MAX_VALUE ? "< " + t : t === Number.MAX_VALUE ? "> " + e : e + " - " + t;
    }, _isContinuity: function _isContinuity() {
      var e = this.dataRangeOption;return !(e.splitList ? e.splitList.length > 0 : e.splitNumber > 0) || e.calculable;
    }, _useCustomizedSplit: function _useCustomizedSplit() {
      var e = this.dataRangeOption;return e.splitList && e.splitList.length > 0;
    }, _buildColorList: function _buildColorList(e) {
      if (this._colorList = d.getGradientColors(this.dataRangeOption.color, Math.max((e - this.dataRangeOption.color.length) / (this.dataRangeOption.color.length - 1), 0) + 1), this._colorList.length > e) {
        for (var t = this._colorList.length, i = [this._colorList[0]], n = t / (e - 1), a = 1; e - 1 > a; a++) {
          i.push(this._colorList[Math.floor(a * n)]);
        }i.push(this._colorList[t - 1]), this._colorList = i;
      }if (this._useCustomizedSplit()) for (var o = this._splitList, a = 0, t = o.length; t > a; a++) {
        o[a].color && (this._colorList[a] = o[a].color);
      }
    }, _buildGap: function _buildGap(e) {
      if (!this._useCustomizedSplit()) {
        var t = this.dataRangeOption.precision;for (this._gap = (this.dataRangeOption.max - this.dataRangeOption.min) / e; this._gap.toFixed(t) - 0 != this._gap && 5 > t;) {
          t++;
        }this.dataRangeOption.precision = t, this._gap = ((this.dataRangeOption.max - this.dataRangeOption.min) / e).toFixed(t) - 0;
      }
    }, _buildDataList: function _buildDataList(e) {
      for (var t = this._valueTextList = [], i = this.dataRangeOption, n = this._useCustomizedSplit(), a = 0; e > a; a++) {
        this._selectedMap[a] = !0;var o = "";if (n) {
          var r = this._splitList[e - 1 - a];o = null != r.label ? r.label : null != r.single ? this._textFormat(r.single) : this._textFormat(r.min, r.max);
        } else o = this._textFormat(a * this._gap + i.min, (a + 1) * this._gap + i.min);t.unshift(o);
      }
    }, _buildSplitList: function _buildSplitList() {
      if (this._useCustomizedSplit()) for (var e = this.dataRangeOption.splitList, t = this._splitList = [], i = 0, n = e.length; n > i; i++) {
        var a = e[i];if (!a || null == a.start && null == a.end) throw new Error("Empty item exists in splitList!");var o = { label: a.label, color: a.color };o.min = a.start, o.max = a.end, o.min > o.max && (o.min = [o.max, o.max = o.min][0]), o.min === o.max && (o.single = o.max), null == o.min && (o.min = -Number.MAX_VALUE), null == o.max && (o.max = Number.MAX_VALUE), t.push(o);
      }
    }, refresh: function refresh(e) {
      if (e) {
        this.option = e, this.option.dataRange = this.reformOption(this.option.dataRange);var t = this.dataRangeOption = this.option.dataRange;if (!this._useCustomizedSplit() && (null == t.min || null == t.max)) throw new Error("option.dataRange.min or option.dataRange.max has not been defined.");this.myChart.canvasSupported || (t.realtime = !1);var i = this._isContinuity() ? 100 : this._useCustomizedSplit() ? t.splitList.length : t.splitNumber;this._buildSplitList(), this._buildColorList(i), this._buildGap(i), this._buildDataList(i);
      }this.clear(), this._buildShape();
    }, getColor: function getColor(e) {
      if (isNaN(e)) return null;var t;if (this._useCustomizedSplit()) {
        for (var i = this._splitList, n = 0, a = i.length; a > n; n++) {
          if (i[n].min <= e && i[n].max >= e) {
            t = n;break;
          }
        }
      } else {
        if (this.dataRangeOption.min == this.dataRangeOption.max) return this._colorList[0];if (e < this.dataRangeOption.min ? e = this.dataRangeOption.min : e > this.dataRangeOption.max && (e = this.dataRangeOption.max), this.dataRangeOption.calculable && (e - (this._gap * this._range.start + this.dataRangeOption.min) > 5e-5 || e - (this._gap * this._range.end + this.dataRangeOption.min) < -5e-5)) return null;t = this._colorList.length - Math.ceil((e - this.dataRangeOption.min) / (this.dataRangeOption.max - this.dataRangeOption.min) * this._colorList.length), t == this._colorList.length && t--;
      }return this._selectedMap[t] ? this._colorList[t] : null;
    }, getColorByIndex: function getColorByIndex(e) {
      return e >= this._colorList.length ? e = this._colorList.length - 1 : 0 > e && (e = 0), this._colorList[e];
    }, onbeforDispose: function onbeforDispose() {
      this.messageCenter.unbind(r.EVENT.HOVER, this._onhoverlink);
    } }, s.inherits(t, i), __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../component\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())).define("dataRange", t), t;
}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)), !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__, !(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/shape/Base\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/shape/Polygon\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/tool/util\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())], __WEBPACK_AMD_DEFINE_RESULT__ = (function (e) {
  function t(e) {
    i.call(this, e);
  }var i = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/shape/Base\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())),
      n = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/shape/Polygon\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())),
      a = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/tool/util\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));return t.prototype = { type: "handle-polygon", buildPath: function buildPath(e, t) {
      n.prototype.buildPath(e, t);
    }, isCover: function isCover(e, t) {
      var i = this.transformCoordToLocal(e, t);e = i[0], t = i[1];var n = this.style.rect;return e >= n.x && e <= n.x + n.width && t >= n.y && t <= n.y + n.height ? !0 : !1;
    } }, a.inherits(t, i), t;
}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));

/***/ }),

/***/ 48:
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;!(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__, !(function webpackMissingModule() { var e = new Error("Cannot find module \"./base\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../util/shape/Candle\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../component/axis\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../component/grid\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../component/dataZoom\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../config\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../util/ecData\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/tool/util\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()), !(function webpackMissingModule() { var e = new Error("Cannot find module \"../chart\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())], __WEBPACK_AMD_DEFINE_RESULT__ = (function (e) {
  function t(e, t, n, a, o) {
    i.call(this, e, t, n, a, o), this.refresh(a);
  }var i = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"./base\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())),
      n = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../util/shape/Candle\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));__webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../component/axis\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())), __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../component/grid\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())), __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../component/dataZoom\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));var a = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../config\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));a.k = { zlevel: 0, z: 2, clickable: !0, hoverable: !0, legendHoverLink: !1, xAxisIndex: 0, yAxisIndex: 0, itemStyle: { normal: { color: "#fff", color0: "#00aa11", lineStyle: { width: 1, color: "#ff3200", color0: "#00aa11" }, label: { show: !1 } }, emphasis: { label: { show: !1 } } } };var o = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../util/ecData\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())),
      r = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"zrender/tool/util\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));return t.prototype = { type: a.CHART_TYPE_K, _buildShape: function _buildShape() {
      var e = this.series;this.selectedMap = {};for (var t, i = { top: [], bottom: [] }, n = 0, o = e.length; o > n; n++) {
        e[n].type === a.CHART_TYPE_K && (e[n] = this.reformOption(e[n]), this.legendHoverLink = e[n].legendHoverLink || this.legendHoverLink, t = this.component.xAxis.getAxis(e[n].xAxisIndex), t.type === a.COMPONENT_TYPE_AXIS_CATEGORY && i[t.getPosition()].push(n));
      }for (var r in i) {
        i[r].length > 0 && this._buildSinglePosition(r, i[r]);
      }this.addShapeList();
    }, _buildSinglePosition: function _buildSinglePosition(e, t) {
      var i = this._mapData(t),
          n = i.locationMap,
          a = i.maxDataLength;if (0 !== a && 0 !== n.length) {
        this._buildHorizontal(t, a, n);for (var o = 0, r = t.length; r > o; o++) {
          this.buildMark(t[o]);
        }
      }
    }, _mapData: function _mapData(e) {
      for (var t, i, n = this.series, a = this.component.legend, o = [], r = 0, s = 0, l = e.length; l > s; s++) {
        t = n[e[s]], i = t.name, this.selectedMap[i] = a ? a.isSelected(i) : !0, this.selectedMap[i] && o.push(e[s]), r = Math.max(r, t.data.length);
      }return { locationMap: o, maxDataLength: r };
    }, _buildHorizontal: function _buildHorizontal(e, t, i) {
      for (var n, a, o, r, s, l, h, d, c, m, p = this.series, u = {}, g = 0, V = i.length; V > g; g++) {
        n = i[g], a = p[n], o = a.xAxisIndex || 0, r = this.component.xAxis.getAxis(o), h = a.barWidth || Math.floor(r.getGap() / 2), m = a.barMaxWidth, m && h > m && (h = m), s = a.yAxisIndex || 0, l = this.component.yAxis.getAxis(s), u[n] = [];for (var U = 0, y = t; y > U && null != r.getNameByIndex(U); U++) {
          d = a.data[U], c = this.getDataFromOption(d, "-"), "-" !== c && 4 == c.length && u[n].push([r.getCoordByIndex(U), h, l.getCoord(c[0]), l.getCoord(c[1]), l.getCoord(c[2]), l.getCoord(c[3]), U, r.getNameByIndex(U)]);
        }
      }this._buildKLine(e, u);
    }, _buildKLine: function _buildKLine(e, t) {
      for (var i, n, o, r, s, l, h, d, c, m, p, u, g, V, U, y, f, _ = this.series, b = 0, x = e.length; x > b; b++) {
        if (f = e[b], p = _[f], V = t[f], this._isLarge(V) && (V = this._getLargePointList(V)), p.type === a.CHART_TYPE_K && null != V) {
          u = p, i = this.query(u, "itemStyle.normal.lineStyle.width"), n = this.query(u, "itemStyle.normal.lineStyle.color"), o = this.query(u, "itemStyle.normal.lineStyle.color0"), r = this.query(u, "itemStyle.normal.color"), s = this.query(u, "itemStyle.normal.color0"), l = this.query(u, "itemStyle.emphasis.lineStyle.width"), h = this.query(u, "itemStyle.emphasis.lineStyle.color"), d = this.query(u, "itemStyle.emphasis.lineStyle.color0"), c = this.query(u, "itemStyle.emphasis.color"), m = this.query(u, "itemStyle.emphasis.color0");for (var k = 0, v = V.length; v > k; k++) {
            U = V[k], g = p.data[U[6]], u = g, y = U[3] < U[2], this.shapeList.push(this._getCandle(f, U[6], U[7], U[0], U[1], U[2], U[3], U[4], U[5], y ? this.query(u, "itemStyle.normal.color") || r : this.query(u, "itemStyle.normal.color0") || s, this.query(u, "itemStyle.normal.lineStyle.width") || i, y ? this.query(u, "itemStyle.normal.lineStyle.color") || n : this.query(u, "itemStyle.normal.lineStyle.color0") || o, y ? this.query(u, "itemStyle.emphasis.color") || c || r : this.query(u, "itemStyle.emphasis.color0") || m || s, this.query(u, "itemStyle.emphasis.lineStyle.width") || l || i, y ? this.query(u, "itemStyle.emphasis.lineStyle.color") || h || n : this.query(u, "itemStyle.emphasis.lineStyle.color0") || d || o));
          }
        }
      }
    }, _isLarge: function _isLarge(e) {
      return e[0][1] < .5;
    }, _getLargePointList: function _getLargePointList(e) {
      for (var t = this.component.grid.getWidth(), i = e.length, n = [], a = 0; t > a; a++) {
        n[a] = e[Math.floor(i / t * a)];
      }return n;
    }, _getCandle: function _getCandle(e, t, i, a, r, s, l, h, d, c, m, p, u, g, V) {
      var U = this.series,
          y = U[e],
          f = y.data[t],
          _ = [f, y],
          b = { zlevel: y.zlevel, z: y.z, clickable: this.deepQuery(_, "clickable"), hoverable: this.deepQuery(_, "hoverable"), style: { x: a, y: [s, l, h, d], width: r, color: c, strokeColor: p, lineWidth: m, brushType: "both" }, highlightStyle: { color: u, strokeColor: V, lineWidth: g }, _seriesIndex: e };return b = this.addLabel(b, y, f, i), o.pack(b, y, e, f, t, i), b = new n(b);
    }, getMarkCoord: function getMarkCoord(e, t) {
      var i = this.series[e],
          n = this.component.xAxis.getAxis(i.xAxisIndex),
          a = this.component.yAxis.getAxis(i.yAxisIndex);return ["string" != typeof t.xAxis && n.getCoordByIndex ? n.getCoordByIndex(t.xAxis || 0) : n.getCoord(t.xAxis || 0), "string" != typeof t.yAxis && a.getCoordByIndex ? a.getCoordByIndex(t.yAxis || 0) : a.getCoord(t.yAxis || 0)];
    }, refresh: function refresh(e) {
      e && (this.option = e, this.series = e.series), this.backupShapeList(), this._buildShape();
    }, addDataAnimation: function addDataAnimation(e, t) {
      function i() {
        u--, 0 === u && t && t();
      }for (var n = this.series, a = {}, r = 0, s = e.length; s > r; r++) {
        a[e[r][0]] = e[r];
      }for (var l, h, d, c, m, p, u = 0, r = 0, s = this.shapeList.length; s > r; r++) {
        if (m = this.shapeList[r]._seriesIndex, a[m] && !a[m][3] && "candle" === this.shapeList[r].type) {
          if (p = o.get(this.shapeList[r], "dataIndex"), c = n[m], a[m][2] && p === c.data.length - 1) {
            this.zr.delShape(this.shapeList[r].id);continue;
          }if (!a[m][2] && 0 === p) {
            this.zr.delShape(this.shapeList[r].id);continue;
          }h = this.component.xAxis.getAxis(c.xAxisIndex || 0).getGap(), l = a[m][2] ? h : -h, d = 0, u++, this.zr.animate(this.shapeList[r].id, "").when(this.query(this.option, "animationDurationUpdate"), { position: [l, d] }).done(i).start();
        }
      }u || t && t();
    } }, r.inherits(t, i), __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../chart\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())).define("k", t), t;
}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));

/***/ })

});