/*
 Copyright 2009 Google Inc.

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
 */
(function () {
    var h = {isnamespace_: true};
    if (!("map" in Array.prototype)) {
        Array.prototype.map = function (p) {
            var l = this.length;
            if (typeof p != "function") {throw new TypeError("map() requires a mapping function.")}
            var o = new Array(l);
            var n = arguments[1];
            for (var m = 0; m < l; m++) {if (m in this) {o[m] = p.call(n, this[m], m, this)}}
            return o
        }
    }
    h.ALTITUDE_CLAMP_TO_GROUND = 0;
    h.ALTITUDE_RELATIVE_TO_GROUND = 1;
    h.ALTITUDE_ABSOLUTE = 2;
    h.ALTITUDE_CLAMP_TO_SEA_FLOOR = 4;
    h.ALTITUDE_RELATIVE_TO_SEA_FLOOR = 5;
    var f = {precision: 0.000001};

    function i() {}

    i.prototype = {e: function (l) {return(l < 1 || l > this.elements.length) ? null : this.elements[l - 1]}, dimensions: function () {return this.elements.length}, modulus: function () {return Math.sqrt(this.dot(this))}, eql: function (m) {
        var o = this.elements.length;
        var l = m.elements || m;
        if (o != l.length) {return false}
        while (o--) {if (Math.abs(this.elements[o] - l[o]) > f.precision) {return false}}
        return true
    }, dup: function () {return i.create(this.elements)}, map: function (l) {
        var m = [];
        this.each(function (n, o) {m.push(l(n, o))});
        return i.create(m)
    }, each: function (m) {
        var o = this.elements.length;
        for (var l = 0; l < o; l++) {m(this.elements[l], l + 1)}
    }, toUnitVector: function () {
        var l = this.modulus();
        if (l === 0) {return this.dup()}
        return this.map(function (m) {return m / l})
    }, angleFrom: function (p) {
        var q = p.elements || p;
        var o = this.elements.length, r = o, s;
        if (o != q.length) {return null}
        var l = 0, u = 0, t = 0;
        this.each(function (n, v) {
            l += n * q[v - 1];
            u += n * n;
            t += q[v - 1] * q[v - 1]
        });
        u = Math.sqrt(u);
        t = Math.sqrt(t);
        if (u * t === 0) {return null}
        var m = l / (u * t);
        if (m < -1) {m = -1}
        if (m > 1) {m = 1}
        return Math.acos(m)
    }, isParallelTo: function (l) {
        var m = this.angleFrom(l);
        return(m === null) ? null : (m <= f.precision)
    }, isAntiparallelTo: function (l) {
        var m = this.angleFrom(l);
        return(m === null) ? null : (Math.abs(m - Math.PI) <= f.precision)
    }, isPerpendicularTo: function (l) {
        var m = this.dot(l);
        return(m === null) ? null : (Math.abs(m) <= f.precision)
    }, add: function (m) {
        var l = m.elements || m;
        if (this.elements.length != l.length) {return null}
        return this.map(function (n, o) {return n + l[o - 1]})
    }, subtract: function (m) {
        var l = m.elements || m;
        if (this.elements.length != l.length) {return null}
        return this.map(function (n, o) {return n - l[o - 1]})
    }, multiply: function (l) {return this.map(function (m) {return m * l})}, x: function (l) {return this.multiply(l)}, dot: function (m) {
        var l = m.elements || m;
        var o, p = 0, q = this.elements.length;
        if (q != l.length) {return null}
        while (q--) {p += this.elements[q] * l[q]}
        return p
    }, cross: function (m) {
        var n = m.elements || m;
        if (this.elements.length != 3 || n.length != 3) {return null}
        var l = this.elements;
        return i.create([(l[1] * n[2]) - (l[2] * n[1]), (l[2] * n[0]) - (l[0] * n[2]), (l[0] * n[1]) - (l[1] * n[0])])
    }, max: function () {
        var l = 0, n = this.elements.length;
        while (n--) {if (Math.abs(this.elements[n]) > Math.abs(l)) {l = this.elements[n]}}
        return l
    }, indexOf: function (l) {
        var m = null, p = this.elements.length;
        for (var o = 0; o < p; o++) {if (m === null && this.elements[o] == l) {m = o + 1}}
        return m
    }, toDiagonalMatrix: function () {return b.Diagonal(this.elements)}, round: function () {return this.map(function (l) {return Math.round(l)})}, snapTo: function (l) {return this.map(function (m) {return(Math.abs(m - l) <= f.precision) ? l : m})}, distanceFrom: function (o) {
        if (o.anchor || (o.start && o.end)) {return o.distanceFrom(this)}
        var l = o.elements || o;
        if (l.length != this.elements.length) {return null}
        var n = 0, m;
        this.each(function (p, q) {
            m = p - l[q - 1];
            n += m * m
        });
        return Math.sqrt(n)
    }, liesOn: function (l) {return l.contains(this)}, liesIn: function (l) {return l.contains(this)}, rotate: function (n, p) {
        var m, o = null, l, s, r;
        if (n.determinant) {o = n.elements}
        switch (this.elements.length) {
            case 2:
                m = p.elements || p;
                if (m.length != 2) {return null}
                if (!o) {o = b.Rotation(n).elements}
                l = this.elements[0] - m[0];
                s = this.elements[1] - m[1];
                return i.create([m[0] + o[0][0] * l + o[0][1] * s, m[1] + o[1][0] * l + o[1][1] * s]);
                break;
            case 3:
                if (!p.direction) {return null}
                var q = p.pointClosestTo(this).elements;
                if (!o) {o = b.Rotation(n, p.direction).elements}
                l = this.elements[0] - q[0];
                s = this.elements[1] - q[1];
                r = this.elements[2] - q[2];
                return i.create([q[0] + o[0][0] * l + o[0][1] * s + o[0][2] * r, q[1] + o[1][0] * l + o[1][1] * s + o[1][2] * r, q[2] + o[2][0] * l + o[2][1] * s + o[2][2] * r]);
                break;
            default:
                return null
        }
    }, reflectionIn: function (n) {
        if (n.anchor) {
            var m = this.elements.slice();
            var o = n.pointClosestTo(m).elements;
            return i.create([o[0] + (o[0] - m[0]), o[1] + (o[1] - m[1]), o[2] + (o[2] - (m[2] || 0))])
        } else {
            var l = n.elements || n;
            if (this.elements.length != l.length) {return null}
            return this.map(function (p, q) {return l[q - 1] + (l[q - 1] - p)})
        }
    }, to3D: function () {
        var l = this.dup();
        switch (l.elements.length) {
            case 3:
                break;
            case 2:
                l.elements.push(0);
                break;
            default:
                return null
        }
        return l
    }, inspect: function () {return"[" + this.elements.join(", ") + "]"}, setElements: function (l) {
        this.elements = (l.elements || l).slice();
        return this
    }};
    i.create = function (m) {
        var l = new i();
        return l.setElements(m)
    };
    var k = i.create;
    i.i = i.create([1, 0, 0]);
    i.j = i.create([0, 1, 0]);
    i.k = i.create([0, 0, 1]);
    i.Random = function (m) {
        var l = [];
        while (m--) {l.push(Math.random())}
        return i.create(l)
    };
    i.Zero = function (m) {
        var l = [];
        while (m--) {l.push(0)}
        return i.create(l)
    };
    function b() {}

    b.prototype = {e: function (m, l) {
        if (m < 1 || m > this.elements.length || l < 1 || l > this.elements[0].length) {return null}
        return this.elements[m - 1][l - 1]
    }, row: function (l) {
        if (l > this.elements.length) {return null}
        return i.create(this.elements[l - 1])
    }, col: function (m) {
        if (m > this.elements[0].length) {return null}
        var l = [], p = this.elements.length;
        for (var o = 0; o < p; o++) {l.push(this.elements[o][m - 1])}
        return i.create(l)
    }, dimensions: function () {return{rows: this.elements.length, cols: this.elements[0].length}}, rows: function () {return this.elements.length}, cols: function () {return this.elements[0].length}, eql: function (l) {
        var p = l.elements || l;
        if (typeof(p[0][0]) == "undefined") {p = b.create(p).elements}
        if (this.elements.length != p.length || this.elements[0].length != p[0].length) {return false}
        var o = this.elements.length, n = this.elements[0].length, m;
        while (o--) {
            m = n;
            while (m--) {if (Math.abs(this.elements[o][m] - p[o][m]) > f.precision) {return false}}
        }
        return true
    }, dup: function () {return b.create(this.elements)}, map: function (p) {
        var o = [], n = this.elements.length, m = this.elements[0].length, l;
        while (n--) {
            l = m;
            o[n] = [];
            while (l--) {o[n][l] = p(this.elements[n][l], n + 1, l + 1)}
        }
        return b.create(o)
    }, isSameSizeAs: function (l) {
        var m = l.elements || l;
        if (typeof(m[0][0]) == "undefined") {m = b.create(m).elements}
        return(this.elements.length == m.length && this.elements[0].length == m[0].length)
    }, add: function (l) {
        var m = l.elements || l;
        if (typeof(m[0][0]) == "undefined") {m = b.create(m).elements}
        if (!this.isSameSizeAs(m)) {return null}
        return this.map(function (n, p, o) {return n + m[p - 1][o - 1]})
    }, subtract: function (l) {
        var m = l.elements || l;
        if (typeof(m[0][0]) == "undefined") {m = b.create(m).elements}
        if (!this.isSameSizeAs(m)) {return null}
        return this.map(function (n, p, o) {return n - m[p - 1][o - 1]})
    }, canMultiplyFromLeft: function (l) {
        var m = l.elements || l;
        if (typeof(m[0][0]) == "undefined") {m = b.create(m).elements}
        return(this.elements[0].length == m.length)
    }, multiply: function (u) {
        if (!u.elements) {return this.map(function (v) {return v * u})}
        var n = u.modulus ? true : false;
        var r = u.elements || u;
        if (typeof(r[0][0]) == "undefined") {r = b.create(r).elements}
        if (!this.canMultiplyFromLeft(r)) {return null}
        var p = this.elements.length, m = r[0].length, o;
        var t = this.elements[0].length, s, l = [], q;
        while (p--) {
            o = m;
            l[p] = [];
            while (o--) {
                s = t;
                q = 0;
                while (s--) {q += this.elements[p][s] * r[s][o]}
                l[p][o] = q
            }
        }
        var r = b.create(l);
        return n ? r.col(1) : r
    }, x: function (l) {return this.multiply(l)}, minor: function (u, t, r, q) {
        var l = [], n = r, p, m, o;
        var v = this.elements.length, s = this.elements[0].length;
        while (n--) {
            p = r - n - 1;
            l[p] = [];
            m = q;
            while (m--) {
                o = q - m - 1;
                l[p][o] = this.elements[(u + p - 1) % v][(t + o - 1) % s]
            }
        }
        return b.create(l)
    }, transpose: function () {
        var o = this.elements.length, m, p = this.elements[0].length, l;
        var n = [], m = p;
        while (m--) {
            l = o;
            n[m] = [];
            while (l--) {n[m][l] = this.elements[l][m]}
        }
        return b.create(n)
    }, isSquare: function () {return(this.elements.length == this.elements[0].length)}, max: function () {
        var l = 0, p = this.elements.length, o = this.elements[0].length, n;
        while (p--) {
            n = o;
            while (n--) {if (Math.abs(this.elements[p][n]) > Math.abs(l)) {l = this.elements[p][n]}}
        }
        return l
    }, indexOf: function (l) {
        var o = null, q = this.elements.length, p, n = this.elements[0].length, m;
        for (p = 0; p < q; p++) {for (m = 0; m < n; m++) {if (this.elements[p][m] == l) {return{i: p + 1, j: m + 1}}}}
        return null
    }, diagonal: function () {
        if (!this.isSquare) {return null}
        var m = [], o = this.elements.length;
        for (var l = 0; l < o; l++) {m.push(this.elements[l][l])}
        return i.create(m)
    }, toRightTriangular: function () {
        var u = this.dup(), o;
        var t = this.elements.length, m, l, r = this.elements[0].length, q;
        for (m = 0; m < t; m++) {
            if (u.elements[m][m] == 0) {
                for (l = m + 1; l < t; l++) {
                    if (u.elements[l][m] != 0) {
                        o = [];
                        for (q = 0; q < r; q++) {o.push(u.elements[m][q] + u.elements[l][q])}
                        u.elements[m] = o;
                        break
                    }
                }
            }
            if (u.elements[m][m] != 0) {
                for (l = m + 1; l < t; l++) {
                    var s = u.elements[l][m] / u.elements[m][m];
                    o = [];
                    for (q = 0; q < r; q++) {o.push(q <= m ? 0 : u.elements[l][q] - u.elements[m][q] * s)}
                    u.elements[l] = o
                }
            }
        }
        return u
    }, toUpperTriangular: function () {return this.toRightTriangular()}, determinant: function () {
        if (!this.isSquare()) {return null}
        var p = this.toRightTriangular();
        var m = p.elements[0][0], o = p.elements.length;
        for (var l = 1; l < o; l++) {m = m * p.elements[l][l]}
        return m
    }, det: function () {return this.determinant()}, isSingular: function () {return(this.isSquare() && this.determinant() === 0)}, trace: function () {
        if (!this.isSquare()) {return null}
        var m = this.elements[0][0], o = this.elements.length;
        for (var l = 1; l < o; l++) {m += this.elements[l][l]}
        return m
    }, tr: function () {return this.trace()}, rank: function () {
        var p = this.toRightTriangular(), o = 0;
        var n = this.elements.length, m = this.elements[0].length, l;
        while (n--) {
            l = m;
            while (l--) {
                if (Math.abs(p.elements[n][l]) > f.precision) {
                    o++;
                    break
                }
            }
        }
        return o
    }, rk: function () {return this.rank()}, augment: function (l) {
        var r = l.elements || l;
        if (typeof(r[0][0]) == "undefined") {r = b.create(r).elements}
        var o = this.dup(), q = o.elements[0].length;
        var p = o.elements.length, n = r[0].length, m;
        if (p != r.length) {return null}
        while (p--) {
            m = n;
            while (m--) {o.elements[p][q + m] = r[p][m]}
        }
        return o
    }, inverse: function () {
        if (!this.isSquare() || this.isSingular()) {return null}
        var o = this.elements.length, t = o, s;
        var u = this.augment(b.I(o)).toRightTriangular();
        var v = u.elements[0].length, l, r, m;
        var w = [], q;
        while (t--) {
            r = [];
            w[t] = [];
            m = u.elements[t][t];
            for (l = 0; l < v; l++) {
                q = u.elements[t][l] / m;
                r.push(q);
                if (l >= o) {w[t].push(q)}
            }
            u.elements[t] = r;
            s = t;
            while (s--) {
                r = [];
                for (l = 0; l < v; l++) {r.push(u.elements[s][l] - u.elements[t][l] * u.elements[s][t])}
                u.elements[s] = r
            }
        }
        return b.create(w)
    }, inv: function () {return this.inverse()}, round: function () {return this.map(function (l) {return Math.round(l)})}, snapTo: function (l) {return this.map(function (m) {return(Math.abs(m - l) <= f.precision) ? l : m})}, inspect: function () {
        var m = [];
        var o = this.elements.length;
        for (var l = 0; l < o; l++) {m.push(i.create(this.elements[l]).inspect())}
        return m.join("\n")
    }, setElements: function (o) {
        var m, l, p = o.elements || o;
        if (typeof(p[0][0]) != "undefined") {
            m = p.length;
            this.elements = [];
            while (m--) {
                l = p[m].length;
                this.elements[m] = [];
                while (l--) {this.elements[m][l] = p[m][l]}
            }
            return this
        }
        var q = p.length;
        this.elements = [];
        for (m = 0; m < q; m++) {this.elements.push([p[m]])}
        return this
    }};
    b.create = function (l) {
        var m = new b();
        return m.setElements(l)
    };
    var d = b.create;
    b.I = function (p) {
        var o = [], m = p, l;
        while (m--) {
            l = p;
            o[m] = [];
            while (l--) {o[m][l] = (m == l) ? 1 : 0}
        }
        return b.create(o)
    };
    b.Diagonal = function (m) {
        var l = m.length;
        var n = b.I(l);
        while (l--) {n.elements[l][l] = m[l]}
        return n
    };
    b.Rotation = function (l, r) {
        if (!r) {
            return b.create([
                [Math.cos(l), -Math.sin(l)],
                [Math.sin(l), Math.cos(l)]
            ])
        }
        var m = r.dup();
        if (m.elements.length != 3) {return null}
        var q = m.modulus();
        var u = m.elements[0] / q, p = m.elements[1] / q, o = m.elements[2] / q;
        var w = Math.sin(l), n = Math.cos(l), v = 1 - n;
        return b.create([
            [v * u * u + n, v * u * p - w * o, v * u * o + w * p],
            [v * u * p + w * o, v * p * p + n, v * p * o - w * u],
            [v * u * o - w * p, v * p * o + w * u, v * o * o + n]
        ])
    };
    b.RotationX = function (l) {
        var n = Math.cos(l), m = Math.sin(l);
        return b.create([
            [1, 0, 0],
            [0, n, -m],
            [0, m, n]
        ])
    };
    b.RotationY = function (l) {
        var n = Math.cos(l), m = Math.sin(l);
        return b.create([
            [n, 0, m],
            [0, 1, 0],
            [-m, 0, n]
        ])
    };
    b.RotationZ = function (l) {
        var n = Math.cos(l), m = Math.sin(l);
        return b.create([
            [n, -m, 0],
            [m, n, 0],
            [0, 0, 1]
        ])
    };
    b.Random = function (o, l) {return b.Zero(o, l).map(function () {return Math.random()})};
    b.Zero = function (r, l) {
        var q = [], p = r, o;
        while (p--) {
            o = l;
            q[p] = [];
            while (o--) {q[p][o] = 0}
        }
        return b.create(q)
    };
    function g() {}

    g.prototype = {eql: function (l) {return(this.isParallelTo(l) && this.contains(l.anchor))}, dup: function () {return g.create(this.anchor, this.direction)}, translate: function (m) {
        var l = m.elements || m;
        return g.create([this.anchor.elements[0] + l[0], this.anchor.elements[1] + l[1], this.anchor.elements[2] + (l[2] || 0)], this.direction)
    }, isParallelTo: function (m) {
        if (m.normal || (m.start && m.end)) {return m.isParallelTo(this)}
        var l = this.direction.angleFrom(m.direction);
        return(Math.abs(l) <= f.precision || Math.abs(l - Math.PI) <= f.precision)
    }, distanceFrom: function (p) {
        if (p.normal || (p.start && p.end)) {return p.distanceFrom(this)}
        if (p.direction) {
            if (this.isParallelTo(p)) {return this.distanceFrom(p.anchor)}
            var t = this.direction.cross(p.direction).toUnitVector().elements;
            var n = this.anchor.elements, m = p.anchor.elements;
            return Math.abs((n[0] - m[0]) * t[0] + (n[1] - m[1]) * t[1] + (n[2] - m[2]) * t[2])
        } else {
            var q = p.elements || p;
            var n = this.anchor.elements, l = this.direction.elements;
            var w = q[0] - n[0], u = q[1] - n[1], r = (q[2] || 0) - n[2];
            var v = Math.sqrt(w * w + u * u + r * r);
            if (v === 0) {return 0}
            var s = (w * l[0] + u * l[1] + r * l[2]) / v;
            var o = 1 - s * s;
            return Math.abs(v * Math.sqrt(o < 0 ? 0 : o))
        }
    }, contains: function (l) {
        if (l.start && l.end) {return this.contains(l.start) && this.contains(l.end)}
        var m = this.distanceFrom(l);
        return(m !== null && m <= f.precision)
    }, positionOf: function (m) {
        if (!this.contains(m)) {return null}
        var n = m.elements || m;
        var l = this.anchor.elements, o = this.direction.elements;
        return(n[0] - l[0]) * o[0] + (n[1] - l[1]) * o[1] + ((n[2] || 0) - l[2]) * o[2]
    }, liesIn: function (l) {return l.contains(this)}, intersects: function (l) {
        if (l.normal) {return l.intersects(this)}
        return(!this.isParallelTo(l) && this.distanceFrom(l) <= f.precision)
    }, intersectionWith: function (w) {
        if (w.normal || (w.start && w.end)) {return w.intersectionWith(this)}
        if (!this.intersects(w)) {return null}
        var u = this.anchor.elements, m = this.direction.elements, t = w.anchor.elements, l = w.direction.elements;
        var E = m[0], D = m[1], C = m[2], s = l[0], r = l[1], p = l[2];
        var A = u[0] - t[0], z = u[1] - t[1], y = u[2] - t[2];
        var v = -E * A - D * z - C * y;
        var o = s * A + r * z + p * y;
        var q = E * E + D * D + C * C;
        var B = s * s + r * r + p * p;
        var n = E * s + D * r + C * p;
        var x = (v * B / q + n * o) / (B - n * n);
        return i.create([u[0] + x * E, u[1] + x * D, u[2] + x * C])
    }, pointClosestTo: function (G) {
        if (G.start && G.end) {
            var s = G.pointClosestTo(this);
            return(s === null) ? null : this.pointClosestTo(s)
        } else {
            if (G.direction) {
                if (this.intersects(G)) {return this.intersectionWith(G)}
                if (this.isParallelTo(G)) {return null}
                var I = this.direction.elements, H = G.direction.elements;
                var q = I[0], p = I[1], n = I[2], C = H[0], w = H[1], u = H[2];
                var F = (n * C - q * u), B = (q * w - p * C), v = (p * u - n * w);
                var t = [F * u - B * w, B * C - v * u, v * w - F * C];
                var s = Plane.create(G.anchor, t);
                return s.intersectionWith(this)
            } else {
                var s = G.elements || G;
                if (this.contains(s)) {return i.create(s)}
                var J = this.anchor.elements, I = this.direction.elements;
                var q = I[0], p = I[1], n = I[2], o = J[0], m = J[1], l = J[2];
                var F = q * (s[1] - m) - p * (s[0] - o), B = p * ((s[2] || 0) - l) - n * (s[1] - m), v = n * (s[0] - o) - q * ((s[2] || 0) - l);
                var r = i.create([p * F - n * v, n * B - q * F, q * v - p * B]);
                var K = this.distanceFrom(s) / r.modulus();
                return i.create([s[0] + r.elements[0] * K, s[1] + r.elements[1] * K, (s[2] || 0) + r.elements[2] * K])
            }
        }
    }, rotate: function (F, G) {
        if (typeof(G.direction) == "undefined") {G = g.create(G.to3D(), i.k)}
        var r = b.Rotation(F, G.direction).elements;
        var m = G.pointClosestTo(this.anchor).elements;
        var o = this.anchor.elements, l = this.direction.elements;
        var v = m[0], u = m[1], s = m[2], q = o[0], p = o[1], n = o[2];
        var E = q - v, B = p - u, w = n - s;
        return g.create([v + r[0][0] * E + r[0][1] * B + r[0][2] * w, u + r[1][0] * E + r[1][1] * B + r[1][2] * w, s + r[2][0] * E + r[2][1] * B + r[2][2] * w], [r[0][0] * l[0] + r[0][1] * l[1] + r[0][2] * l[2], r[1][0] * l[0] + r[1][1] * l[1] + r[1][2] * l[2], r[2][0] * l[0] + r[2][1] * l[1] + r[2][2] * l[2]])
    }, reverse: function () {return g.create(this.anchor, this.direction.x(-1))}, reflectionIn: function (x) {
        if (x.normal) {
            var q = this.anchor.elements, m = this.direction.elements;
            var u = q[0], s = q[1], p = q[2], r = m[0], o = m[1], n = m[2];
            var l = this.anchor.reflectionIn(x).elements;
            var w = u + r, v = s + o, t = p + n;
            var y = x.pointClosestTo([w, v, t]).elements;
            var B = [y[0] + (y[0] - w) - l[0], y[1] + (y[1] - v) - l[1], y[2] + (y[2] - t) - l[2]];
            return g.create(l, B)
        } else {
            if (x.direction) {return this.rotate(Math.PI, x)} else {
                var z = x.elements || x;
                return g.create(this.anchor.reflectionIn([z[0], z[1], (z[2] || 0)]), this.direction)
            }
        }
    }, setVectors: function (l, n) {
        l = i.create(l);
        n = i.create(n);
        if (l.elements.length == 2) {l.elements.push(0)}
        if (n.elements.length == 2) {n.elements.push(0)}
        if (l.elements.length > 3 || n.elements.length > 3) {return null}
        var m = n.modulus();
        if (m === 0) {return null}
        this.anchor = l;
        this.direction = i.create([n.elements[0] / m, n.elements[1] / m, n.elements[2] / m]);
        return this
    }};
    g.create = function (m, n) {
        var l = new g();
        return l.setVectors(m, n)
    };
    var e = g.create;
    g.X = g.create(i.Zero(3), i.i);
    g.Y = g.create(i.Zero(3), i.j);
    g.Z = g.create(i.Zero(3), i.k);
    h.linalg = {};
    h.linalg.Vector = function () {return i.create.apply(null, arguments)};
    h.linalg.Vector.create = i.create;
    h.linalg.Vector.i = i.i;
    h.linalg.Vector.j = i.j;
    h.linalg.Vector.k = i.k;
    h.linalg.Vector.Random = i.Random;
    h.linalg.Vector.Zero = i.Zero;
    h.linalg.Matrix = function () {return b.create.apply(null, arguments)};
    h.linalg.Matrix.create = b.create;
    h.linalg.Matrix.I = b.I;
    h.linalg.Matrix.Random = b.Random;
    h.linalg.Matrix.Rotation = b.Rotation;
    h.linalg.Matrix.RotationX = b.RotationX;
    h.linalg.Matrix.RotationY = b.RotationY;
    h.linalg.Matrix.RotationZ = b.RotationZ;
    h.linalg.Matrix.Zero = b.Zero;
    h.linalg.Line = function () {return g.create.apply(null, arguments)};
    h.linalg.Line.create = g.create;
    h.linalg.Line.X = g.X;
    h.linalg.Line.Y = g.Y;
    h.linalg.Line.Z = g.Z;
    h.math = {isnamespace_: true};
    if (!("toDegrees" in Number.prototype)) {Number.prototype.toDegrees = function () {return this * 180 / Math.PI}}
    if (!("toRadians" in Number.prototype)) {Number.prototype.toRadians = function () {return this * Math.PI / 180}}
    h.math.normalizeAngle = function (l) {
        l = l % (2 * Math.PI);
        return l >= 0 ? l : l + 2 * Math.PI
    };
    h.math.normalizeLat = function (l) {return Math.max(-90, Math.min(90, l))};
    h.math.normalizeLng = function (l) {
        if (l % 360 == 180) {return 180}
        l = l % 360;
        return l < -180 ? l + 360 : l > 180 ? l - 360 : l
    };
    h.math.reverseAngle = function (l) {return h.math.normalizeAngle(l + Math.PI)};
    h.math.wrapValue = function (n, l, m) {
        if (!l || !h.util.isArray(l) || l.length != 2) {throw new TypeError("The range parameter must be an array of 2 numbers.")}
        if (n === l[0]) {return l[0]}
        n -= l[0];
        n = n % (l[1] - l[0]);
        if (n < 0) {n += (l[1] - l[0])}
        n += l[0];
        return(n === l[0]) ? (m ? l[0] : l[1]) : n
    };
    h.math.constrainValue = function (m, l) {
        if (!l || !h.util.isArray(l) || l.length != 2) {throw new TypeError("The range parameter must be an array of 2 numbers.")}
        return Math.max(l[0], Math.min(l[1], m))
    };
    h.math.EARTH_RADIUS = 6378135;
    h.math.EARTH_RADIUS_CURVATURE_AVG = 6372795;
    h.math.distance = function (m, l) {return h.math.EARTH_RADIUS * h.math.angularDistance(m, l)};
    h.math.angularDistance = function (r, q) {
        var p = r.lat().toRadians();
        var n = q.lat().toRadians();
        var o = (q.lat() - r.lat()).toRadians();
        var m = (q.lng() - r.lng()).toRadians();
        var l = Math.pow(Math.sin(o / 2), 2) + Math.cos(p) * Math.cos(n) * Math.pow(Math.sin(m / 2), 2);
        return 2 * Math.atan2(Math.sqrt(l), Math.sqrt(1 - l))
    };
    h.math.heading = function (q, o) {
        var n = q.lat().toRadians();
        var m = o.lat().toRadians();
        var p = Math.cos(m);
        var l = (o.lng() - q.lng()).toRadians();
        return h.math.normalizeAngle(Math.atan2(Math.sin(l) * p, Math.cos(n) * Math.sin(m) - Math.sin(n) * p * Math.cos(l))).toDegrees()
    };
    h.math.bearing = h.math.heading;
    h.math.midpoint = function (w, t, F) {
        if (h.util.isUndefined(F) || F === null) {F = 0.5}
        if (w.equals(t)) {return new h.Point(w)}
        var C = w.lat().toRadians();
        var u = t.lat().toRadians();
        var r = w.lng().toRadians();
        var q = t.lng().toRadians();
        var n = Math.cos(C);
        var l = Math.cos(u);
        var E = h.math.angularDistance(w, t);
        var p = Math.sin(E);
        var o = Math.sin((1 - F) * E) / p;
        var m = Math.sin(F * E) / p;
        var D = o * n * Math.cos(r) + m * l * Math.cos(q);
        var v = o * n * Math.sin(r) + m * l * Math.sin(q);
        var s = o * Math.sin(C) + m * Math.sin(u);
        return new h.Point(Math.atan2(s, Math.sqrt(Math.pow(D, 2) + Math.pow(v, 2))).toDegrees(), Math.atan2(v, D).toDegrees())
    };
    h.math.destination = function (m, t) {
        if (!("heading" in t && "distance" in t)) {throw new TypeError("destination() requres both heading and distance options.")}
        var r = m.lat().toRadians();
        var p = Math.sin(r);
        var s = t.distance / h.math.EARTH_RADIUS;
        var l = t.heading.toRadians();
        var o = Math.sin(s);
        var n = Math.cos(s);
        var q = Math.asin(p * n + Math.cos(r) * o * Math.cos(l));
        return new h.Point(q.toDegrees(), Math.atan2(Math.sin(l) * o * Math.cos(q), n - p * Math.sin(q)).toDegrees() + m.lng())
    };
    h.Point = function () {
        var m = null;
        if (arguments.length == 1) {
            var l = arguments[0];
            if (l.constructor === h.Point) {
                this.lat_ = l.lat();
                this.lng_ = l.lng();
                this.altitude_ = l.altitude();
                this.altitudeMode_ = l.altitudeMode()
            } else {
                if (h.util.isArray(l)) {m = l} else {
                    if (j(l)) {
                        var o = l.getType();
                        if (o == "KmlPoint" || o == "KmlLookAt") {
                            this.lat_ = l.getLatitude();
                            this.lng_ = l.getLongitude();
                            this.altitude_ = l.getAltitude();
                            this.altitudeMode_ = l.getAltitudeMode()
                        } else {
                            if (o == "KmlCoord" || o == "KmlLocation") {
                                this.lat_ = l.getLatitude();
                                this.lng_ = l.getLongitude();
                                this.altitude_ = l.getAltitude()
                            } else {throw new TypeError("Could not create a point from the given Earth object")}
                        }
                    } else {
                        if (a(l)) {
                            this.lat_ = l.lat();
                            this.lng_ = l.lng()
                        } else {throw new TypeError("Could not create a point from the given arguments")}
                    }
                }
            }
        } else {m = arguments}
        if (m) {
            for (var n = 0; n < m.length; n++) {if (typeof m[n] != "number") {throw new TypeError("Coordinates must be numerical")}}
            this.lat_ = m[0];
            this.lng_ = m[1];
            if (m.length >= 3) {
                this.altitude_ = m[2];
                if (m.length >= 4) {this.altitudeMode_ = m[3]}
            }
        }
        this.lat_ = h.math.normalizeLat(this.lat_);
        this.lng_ = h.math.normalizeLng(this.lng_)
    };
    h.Point.prototype.lat = function () {return this.lat_};
    h.Point.prototype.lat_ = 0;
    h.Point.prototype.lng = function () {return this.lng_};
    h.Point.prototype.lng_ = 0;
    h.Point.prototype.altitude = function () {return this.altitude_};
    h.Point.prototype.altitude_ = 0;
    h.Point.prototype.altitudeMode = function () {return this.altitudeMode_};
    h.Point.prototype.altitudeMode_ = h.ALTITUDE_RELATIVE_TO_GROUND;
    h.Point.prototype.toString = function () {return"(" + this.lat().toString() + ", " + this.lng().toString() + ", " + this.altitude().toString() + ")"};
    h.Point.prototype.flatten = function () {return new h.Point(this.lat(), this.lng())};
    h.Point.prototype.is3D = function () {return this.altitude_ !== 0};
    h.Point.prototype.equals = function (l) {return this.lat() == l.lat() && this.lng() == l.lng() && this.altitude() == l.altitude() && this.altitudeMode() == l.altitudeMode()};
    h.Point.prototype.angularDistance = function (l) {return h.math.angularDistance(this, l)};
    h.Point.prototype.distance = function (l) {return h.math.distance(this, l)};
    h.Point.prototype.heading = function (l) {return h.math.heading(this, l)};
    h.Point.prototype.midpoint = function (l, m) {return h.math.midpoint(this, l, m)};
    h.Point.prototype.destination = function (l) {return h.math.destination(this, l)};
    h.Point.prototype.toCartesian = function () {
        var o = Math.sin(this.lng().toRadians());
        var p = Math.cos(this.lng().toRadians());
        var m = Math.sin(this.lat().toRadians());
        var n = Math.cos(this.lat().toRadians());
        var l = h.math.EARTH_RADIUS + this.altitude();
        return new h.linalg.Vector([l * p * n, l * m, l * -o * n])
    };
    h.Point.fromCartesian = function (n) {
        var p = n.distanceFrom(h.linalg.Vector.Zero(3));
        var m = n.toUnitVector();
        var l = p - h.math.EARTH_RADIUS;
        var q = Math.asin(m.e(2)).toDegrees();
        if (q > 90) {q -= 180}
        var o = 0;
        if (Math.abs(q) < 90) {o = -Math.atan2(m.e(3), m.e(1)).toDegrees()}
        return new h.Point(q, o, l)
    };
    h.Bounds = function () {
        if (arguments.length == 1) {
            if (arguments[0].constructor === h.Bounds) {
                var m = arguments[0];
                this.sw_ = new h.Point(m.southWestBottom());
                this.ne_ = new h.Point(m.northEastTop())
            } else {this.sw_ = this.ne_ = new h.Point(arguments[0])}
        } else {
            if (arguments.length == 2) {
                var l = new h.Point(arguments[0]);
                var n = new h.Point(arguments[1]);
                if (!l && !n) {return} else {if (!l) {l = n} else {if (!n) {n = l}}}
                if (l.lat() > n.lat()) {throw new RangeError("Bounds southwest coordinate cannot be north of the northeast coordinate")}
                if (l.altitude() > n.altitude()) {throw new RangeError("Bounds southwest coordinate cannot be north of the northeast coordinate")}
                this.sw_ = l;
                this.ne_ = n
            }
        }
    };
    h.Bounds.prototype.southWestBottom = function () {return this.sw_};
    h.Bounds.prototype.sw_ = null;
    h.Bounds.prototype.south = function () {return !this.isEmpty() ? this.sw_.lat() : null};
    h.Bounds.prototype.west = function () {return !this.isEmpty() ? this.sw_.lng() : null};
    h.Bounds.prototype.bottom = function () {return !this.isEmpty() ? this.sw_.altitude() : null};
    h.Bounds.prototype.northEastTop = function () {return this.ne_};
    h.Bounds.prototype.ne_ = null;
    h.Bounds.prototype.north = function () {return !this.isEmpty() ? this.ne_.lat() : null};
    h.Bounds.prototype.east = function () {return !this.isEmpty() ? this.ne_.lng() : null};
    h.Bounds.prototype.top = function () {return !this.isEmpty() ? this.ne_.altitude() : null};
    h.Bounds.prototype.crossesAntimeridian = function () {return !this.isEmpty() && (this.sw_.lng() > this.ne_.lng())};
    h.Bounds.prototype.is3D = function () {return !this.isEmpty() && (this.sw_.is3D() || this.ne_.is3D())};
    h.Bounds.prototype.containsPoint = function (l) {
        l = new h.Point(l);
        if (this.isEmpty()) {return false}
        if (!(this.south() <= l.lat() && l.lat() <= this.north())) {return false}
        if (this.is3D() && !(this.bottom() <= l.altitude() && l.altitude() <= this.top())) {return false}
        return this.containsLng_(l.lng())
    };
    h.Bounds.prototype.containsLng_ = function (l) {if (this.crossesAntimeridian()) {return(l <= this.east() || l >= this.west())} else {return(this.west() <= l && l <= this.east())}};
    function c(l, m) {return(l > m) ? (m + 360 - l) : (m - l)}

    h.Bounds.prototype.extend = function (s) {
        s = new h.Point(s);
        if (this.containsPoint(s)) {return}
        if (this.isEmpty()) {
            this.sw_ = this.ne_ = s;
            return
        }
        var l = this.bottom();
        var q = this.top();
        if (this.is3D()) {
            l = Math.min(l, s.altitude());
            q = Math.max(q, s.altitude())
        }
        var t = Math.min(this.south(), s.lat());
        var p = Math.max(this.north(), s.lat());
        var n = this.west();
        var m = this.east();
        if (!this.containsLng_(s.lng())) {
            var r = c(n, s.lng());
            var o = c(s.lng(), m);
            if (r <= o) {m = s.lng()} else {n = s.lng()}
        }
        this.sw_ = new h.Point(t, n, l);
        this.ne_ = new h.Point(p, m, q)
    };
    h.Bounds.prototype.span = function () {
        if (this.isEmpty()) {return{lat: 0, lng: 0, altitude: 0}}
        return{lat: (this.ne_.lat() - this.sw_.lat()), lng: c(this.sw_.lng(), this.ne_.lng()), altitude: this.is3D() ? (this.ne_.altitude() - this.sw_.altitude()) : null}
    };
    h.Bounds.prototype.isEmpty = function () {return(this.sw_ === null && this.sw_ === null)};
    h.Bounds.prototype.center = function () {
        if (this.isEmpty()) {return null}
        return new h.Point((this.sw_.lat() + this.ne_.lat()) / 2, this.crossesAntimeridian() ? h.math.normalizeLng(this.sw_.lng() + c(this.sw_.lng(), this.ne_.lng()) / 2) : (this.sw_.lng() + this.ne_.lng()) / 2, (this.sw_.altitude() + this.ne_.altitude()) / 2)
    };
    h.Bounds.prototype.getCenter = h.Bounds.prototype.center;
    h.Bounds.prototype.isFullLat = function () {return !this.isEmpty() && (this.south() == -90 && this.north() == 90)};
    h.Bounds.prototype.isFullLng = function () {return !this.isEmpty() && (this.west() == -180 && this.east() == 180)};
    h.Path = function () {
        this.coords_ = [];
        var l = null;
        var m, q;
        if (arguments.length == 1) {
            var p = arguments[0];
            if (p.constructor === h.Path) {for (m = 0; m < p.numCoords(); m++) {this.coords_.push(new h.Point(p.coord(m)))}} else {
                if (h.util.isArray(p)) {l = p} else {
                    if (j(p)) {
                        var o = p.getType();
                        if (o == "KmlLineString" || o == "KmlLinearRing") {
                            q = p.getCoordinates().getLength();
                            for (m = 0; m < q; m++) {this.coords_.push(new h.Point(p.getCoordinates().get(m)))}
                        } else {throw new TypeError("Could not create a path from the given arguments")}
                    } else {
                        if ("getVertex" in p && "getVertexCount" in p) {
                            q = p.getVertexCount();
                            for (m = 0; m < q; m++) {this.coords_.push(new h.Point(p.getVertex(m)))}
                        } else {throw new TypeError("Could not create a path from the given arguments")}
                    }
                }
            }
        } else {l = arguments}
        if (l) {for (m = 0; m < l.length; m++) {this.coords_.push(new h.Point(l[m]))}}
    };
    h.Path.prototype.coords_ = null;
    h.Path.prototype.toString = function () {return"[" + this.coords_.map(function (l) {return l.toString()}).join(", ") + "]"};
    h.Path.prototype.equals = function (m) {
        for (var l = 0; l < m.numCoords(); l++) {if (!this.coord(l).equals(m.coord(l))) {return false}}
        return true
    };
    h.Path.prototype.numCoords = function () {return this.coords_.length};
    h.Path.prototype.coord = function (l) {return this.coords_[l]};
    h.Path.prototype.prepend = function (l) {this.coords_.unshift(new h.Point(l))};
    h.Path.prototype.append = function (l) {this.coords_.push(new h.Point(l))};
    h.Path.prototype.insert = function (l, m) {this.coords_.splice(l, 0, new h.Point(m))};
    h.Path.prototype.remove = function (l) {this.coords_.splice(l, 1)};
    h.Path.prototype.subPath = function (m, l) {return this.coords_.slice(m, l)};
    h.Path.prototype.reverse = function () {this.coords_.reverse()};
    h.Path.prototype.distance = function () {
        var m = 0;
        for (var l = 0; l < this.coords_.length - 1; l++) {m += this.coords_[l].distance(this.coords_[l + 1])}
        return m
    };
    h.Path.prototype.containsPoint = function (m) {
        var p = false;
        var q = m.lat();
        var l = m.lng();
        for (var o = 0; o < this.coords_.length; o++) {
            var n = (o + 1) % this.coords_.length;
            if (((this.coords_[o].lat() < q && this.coords_[n].lat() >= q) || (this.coords_[n].lat() < q && this.coords_[o].lat() >= q)) && (this.coords_[o].lng() + (q - this.coords_[o].lat()) / (this.coords_[n].lat() - this.coords_[o].lat()) * (this.coords_[n].lng() - this.coords_[o].lng()) < l)) {p = !p}
        }
        return p
    };
    h.Path.prototype.bounds = function () {
        if (!this.numCoords()) {return new h.Bounds()}
        var m = new h.Bounds(this.coord(0));
        var n = this.numCoords();
        for (var l = 1; l < n; l++) {m.extend(this.coord(l))}
        return m
    };
    h.Path.prototype.signedArea_ = function () {
        var u = 0;
        var s = this.bounds();
        var n = s.west();
        var v = s.south();
        var q = this.numCoords();
        for (var p = 0; p < q; p++) {
            var o = (p + 1) % q;
            var m = this.coord(p).distance(new h.Point(this.coord(p).lat(), n));
            var l = this.coord(o).distance(new h.Point(this.coord(o).lat(), n));
            var t = this.coord(p).distance(new h.Point(v, this.coord(p).lng()));
            var r = this.coord(o).distance(new h.Point(v, this.coord(o).lng()));
            u += m * r - l * t
        }
        return u * 0.5
    };
    h.Path.prototype.area = function () {return Math.abs(this.signedArea_())};
    h.Path.prototype.isCounterClockwise_ = function () {return Boolean(this.signedArea_() >= 0)};
    h.Polygon = function () {
        this.outerBoundary_ = new h.Path();
        this.innerBoundaries_ = [];
        var m;
        if (arguments.length === 0) {} else {
            if (arguments.length == 1) {
                var p = arguments[0];
                if (p.constructor === h.Polygon) {
                    this.outerBoundary_ = new h.Path(p.outerBoundary());
                    for (m = 0; m < p.innerBoundaries().length; m++) {this.innerBoundaries_.push(new h.Path(p.innerBoundaries()[m]))}
                } else {
                    if (j(p)) {
                        var o = p.getType();
                        if (o == "KmlLineString" || o == "KmlLinearRing") {this.outerBoundary_ = new h.Path(p)} else {
                            if (o == "KmlPolygon") {
                                this.outerBoundary_ = new h.Path(p.getOuterBoundary());
                                var l = p.getInnerBoundaries().getChildNodes();
                                var q = l.getLength();
                                for (m = 0; m < q; m++) {this.innerBoundaries_.push(new h.Path(l.item(m)))}
                            } else {throw new TypeError("Could not create a polygon from the given arguments")}
                        }
                    } else {this.outerBoundary_ = new h.Path(arguments[0])}
                }
            } else {
                if (arguments[0].length && typeof arguments[0][0] == "number") {this.outerBoundary_ = new h.Path(arguments)} else {
                    if (arguments[1]) {
                        this.outerBoundary_ = new h.Path(arguments[0]);
                        if (!h.util.isArray(arguments[1])) {throw new TypeError("Second argument to geo.Polygon constructor must be an array of paths.")}
                        for (m = 0; m < arguments[1].length; m++) {this.innerBoundaries_.push(new h.Path(arguments[1][m]))}
                    } else {throw new TypeError("Cannot create a path from the given arguments.")}
                }
            }
        }
    };
    h.Polygon.prototype.outerBoundary_ = null;
    h.Polygon.prototype.innerBoundaries_ = null;
    h.Polygon.prototype.toString = function () {return"Polygon: " + this.outerBoundary().toString() + (this.innerBoundaries().length ? ", (" + this.innerBoundaries().length + " inner boundaries)" : "")};
    h.Polygon.prototype.outerBoundary = function () {return this.outerBoundary_};
    h.Polygon.prototype.innerBoundaries = function () {return this.innerBoundaries_};
    h.Polygon.prototype.containsPoint = function (l) {
        if (!this.outerBoundary_.containsPoint(l)) {return false}
        for (var m = 0; m < this.innerBoundaries_.length; m++) {if (this.innerBoundaries_[m].containsPoint(l)) {return false}}
        return true
    };
    h.Polygon.prototype.bounds = function () {return this.outerBoundary_.bounds()};
    h.Polygon.prototype.area = function () {
        var m = this.outerBoundary_.area();
        for (var l = 0; l < this.innerBoundaries_.length; l++) {m -= this.innerBoundaries_[l].area()}
        return m
    };
    h.Polygon.prototype.isCounterClockwise = function () {return this.outerBoundary_.isCounterClockwise_()};
    h.Polygon.prototype.makeCounterClockwise = function () {if (this.isCounterClockwise()) {this.outerBoundary_.reverse()}};
    h.util = {isnamespace_: true};
    h.util.isUndefined = function (l) {return typeof l == "undefined"};
    h.util.isArray = function (l) {return l !== null && typeof l == "object" && "splice" in l && "join" in l};
    h.util.isFunction = function (l) {return l !== null && typeof l == "function" && "call" in l && "apply" in l};
    function j(l) {return l !== null && (typeof l == "function" || typeof l == "object") && "getType" in l}

    h.util.isObjectLiteral = function (l) {return l !== null && typeof l == "object" && l.constructor === Object && !j(l)};
    function a(l) {return(window.google && window.google.maps && window.google.maps.LatLng && l.constructor === window.google.maps.LatLng)}

    window.geo = h
})();