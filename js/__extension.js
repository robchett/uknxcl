(function () {
    var h = {isnamespace_: true};
    if (!("map" in Array.prototype)) {
        Array.prototype.map = function (p) {
            var l = this.length;
            if (typeof p != "function") {
                throw new TypeError("map() requires a mapping function.")
            }
            var o = new Array(l);
            var n = arguments[1];
            for (var m = 0; m < l; m++) {
                if (m in this) {
                    o[m] = p.call(n, this[m], m, this)
                }
            }
            return o
        }
    }
    h.ALTITUDE_CLAMP_TO_GROUND = 0;
    h.ALTITUDE_RELATIVE_TO_GROUND = 1;
    h.ALTITUDE_ABSOLUTE = 2;
    h.ALTITUDE_CLAMP_TO_SEA_FLOOR = 4;
    h.ALTITUDE_RELATIVE_TO_SEA_FLOOR = 5;
    var f = {precision: 0.000001};

    function i() {
    }

    i.prototype = {e: function (l) {
        return(l < 1 || l > this.elements.length) ? null : this.elements[l - 1]
    }, dimensions: function () {
        return this.elements.length
    }, modulus: function () {
        return Math.sqrt(this.dot(this))
    }, eql: function (m) {
        var o = this.elements.length;
        var l = m.elements || m;
        if (o != l.length) {
            return false
        }
        while (o--) {
            if (Math.abs(this.elements[o] - l[o]) > f.precision) {
                return false
            }
        }
        return true
    }, dup: function () {
        return i.create(this.elements)
    }, map: function (l) {
        var m = [];
        this.each(function (n, o) {
            m.push(l(n, o))
        });
        return i.create(m)
    }, each: function (m) {
        var o = this.elements.length;
        for (var l = 0; l < o; l++) {
            m(this.elements[l], l + 1)
        }
    }, toUnitVector: function () {
        var l = this.modulus();
        if (l === 0) {
            return this.dup()
        }
        return this.map(function (m) {
            return m / l
        })
    }, angleFrom: function (p) {
        var q = p.elements || p;
        var o = this.elements.length, r = o, s;
        if (o != q.length) {
            return null
        }
        var l = 0, u = 0, t = 0;
        this.each(function (n, v) {
            l += n * q[v - 1];
            u += n * n;
            t += q[v - 1] * q[v - 1]
        });
        u = Math.sqrt(u);
        t = Math.sqrt(t);
        if (u * t === 0) {
            return null
        }
        var m = l / (u * t);
        if (m < -1) {
            m = -1
        }
        if (m > 1) {
            m = 1
        }
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
        if (this.elements.length != l.length) {
            return null
        }
        return this.map(function (n, o) {
            return n + l[o - 1]
        })
    }, subtract: function (m) {
        var l = m.elements || m;
        if (this.elements.length != l.length) {
            return null
        }
        return this.map(function (n, o) {
            return n - l[o - 1]
        })
    }, multiply: function (l) {
        return this.map(function (m) {
            return m * l
        })
    }, x: function (l) {
        return this.multiply(l)
    }, dot: function (m) {
        var l = m.elements || m;
        var o, p = 0, q = this.elements.length;
        if (q != l.length) {
            return null
        }
        while (q--) {
            p += this.elements[q] * l[q]
        }
        return p
    }, cross: function (m) {
        var n = m.elements || m;
        if (this.elements.length != 3 || n.length != 3) {
            return null
        }
        var l = this.elements;
        return i.create([(l[1] * n[2]) - (l[2] * n[1]), (l[2] * n[0]) - (l[0] * n[2]), (l[0] * n[1]) - (l[1] * n[0])])
    }, max: function () {
        var l = 0, n = this.elements.length;
        while (n--) {
            if (Math.abs(this.elements[n]) > Math.abs(l)) {
                l = this.elements[n]
            }
        }
        return l
    }, indexOf: function (l) {
        var m = null, p = this.elements.length;
        for (var o = 0; o < p; o++) {
            if (m === null && this.elements[o] == l) {
                m = o + 1
            }
        }
        return m
    }, toDiagonalMatrix: function () {
        return b.Diagonal(this.elements)
    }, round: function () {
        return this.map(function (l) {
            return Math.round(l)
        })
    }, snapTo: function (l) {
        return this.map(function (m) {
            return(Math.abs(m - l) <= f.precision) ? l : m
        })
    }, distanceFrom: function (o) {
        if (o.anchor || (o.start && o.end)) {
            return o.distanceFrom(this)
        }
        var l = o.elements || o;
        if (l.length != this.elements.length) {
            return null
        }
        var n = 0, m;
        this.each(function (p, q) {
            m = p - l[q - 1];
            n += m * m
        });
        return Math.sqrt(n)
    }, liesOn: function (l) {
        return l.contains(this)
    }, liesIn: function (l) {
        return l.contains(this)
    }, rotate: function (n, p) {
        var m, o = null, l, s, r;
        if (n.determinant) {
            o = n.elements
        }
        switch (this.elements.length) {
            case 2:
                m = p.elements || p;
                if (m.length != 2) {
                    return null
                }
                if (!o) {
                    o = b.Rotation(n).elements
                }
                l = this.elements[0] - m[0];
                s = this.elements[1] - m[1];
                return i.create([m[0] + o[0][0] * l + o[0][1] * s, m[1] + o[1][0] * l + o[1][1] * s]);
                break;
            case 3:
                if (!p.direction) {
                    return null
                }
                var q = p.pointClosestTo(this).elements;
                if (!o) {
                    o = b.Rotation(n, p.direction).elements
                }
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
            if (this.elements.length != l.length) {
                return null
            }
            return this.map(function (p, q) {
                return l[q - 1] + (l[q - 1] - p)
            })
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
    }, inspect: function () {
        return"[" + this.elements.join(", ") + "]"
    }, setElements: function (l) {
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
        while (m--) {
            l.push(Math.random())
        }
        return i.create(l)
    };
    i.Zero = function (m) {
        var l = [];
        while (m--) {
            l.push(0)
        }
        return i.create(l)
    };
    function b() {
    }

    b.prototype = {e: function (m, l) {
        if (m < 1 || m > this.elements.length || l < 1 || l > this.elements[0].length) {
            return null
        }
        return this.elements[m - 1][l - 1]
    }, row: function (l) {
        if (l > this.elements.length) {
            return null
        }
        return i.create(this.elements[l - 1])
    }, col: function (m) {
        if (m > this.elements[0].length) {
            return null
        }
        var l = [], p = this.elements.length;
        for (var o = 0; o < p; o++) {
            l.push(this.elements[o][m - 1])
        }
        return i.create(l)
    }, dimensions: function () {
        return{rows: this.elements.length, cols: this.elements[0].length}
    }, rows: function () {
        return this.elements.length
    }, cols: function () {
        return this.elements[0].length
    }, eql: function (l) {
        var p = l.elements || l;
        if (typeof(p[0][0]) == "undefined") {
            p = b.create(p).elements
        }
        if (this.elements.length != p.length || this.elements[0].length != p[0].length) {
            return false
        }
        var o = this.elements.length, n = this.elements[0].length, m;
        while (o--) {
            m = n;
            while (m--) {
                if (Math.abs(this.elements[o][m] - p[o][m]) > f.precision) {
                    return false
                }
            }
        }
        return true
    }, dup: function () {
        return b.create(this.elements)
    }, map: function (p) {
        var o = [], n = this.elements.length, m = this.elements[0].length, l;
        while (n--) {
            l = m;
            o[n] = [];
            while (l--) {
                o[n][l] = p(this.elements[n][l], n + 1, l + 1)
            }
        }
        return b.create(o)
    }, isSameSizeAs: function (l) {
        var m = l.elements || l;
        if (typeof(m[0][0]) == "undefined") {
            m = b.create(m).elements
        }
        return(this.elements.length == m.length && this.elements[0].length == m[0].length)
    }, add: function (l) {
        var m = l.elements || l;
        if (typeof(m[0][0]) == "undefined") {
            m = b.create(m).elements
        }
        if (!this.isSameSizeAs(m)) {
            return null
        }
        return this.map(function (n, p, o) {
            return n + m[p - 1][o - 1]
        })
    }, subtract: function (l) {
        var m = l.elements || l;
        if (typeof(m[0][0]) == "undefined") {
            m = b.create(m).elements
        }
        if (!this.isSameSizeAs(m)) {
            return null
        }
        return this.map(function (n, p, o) {
            return n - m[p - 1][o - 1]
        })
    }, canMultiplyFromLeft: function (l) {
        var m = l.elements || l;
        if (typeof(m[0][0]) == "undefined") {
            m = b.create(m).elements
        }
        return(this.elements[0].length == m.length)
    }, multiply: function (u) {
        if (!u.elements) {
            return this.map(function (v) {
                return v * u
            })
        }
        var n = u.modulus ? true : false;
        var r = u.elements || u;
        if (typeof(r[0][0]) == "undefined") {
            r = b.create(r).elements
        }
        if (!this.canMultiplyFromLeft(r)) {
            return null
        }
        var p = this.elements.length, m = r[0].length, o;
        var t = this.elements[0].length, s, l = [], q;
        while (p--) {
            o = m;
            l[p] = [];
            while (o--) {
                s = t;
                q = 0;
                while (s--) {
                    q += this.elements[p][s] * r[s][o]
                }
                l[p][o] = q
            }
        }
        var r = b.create(l);
        return n ? r.col(1) : r
    }, x: function (l) {
        return this.multiply(l)
    }, minor: function (u, t, r, q) {
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
            while (l--) {
                n[m][l] = this.elements[l][m]
            }
        }
        return b.create(n)
    }, isSquare: function () {
        return(this.elements.length == this.elements[0].length)
    }, max: function () {
        var l = 0, p = this.elements.length, o = this.elements[0].length, n;
        while (p--) {
            n = o;
            while (n--) {
                if (Math.abs(this.elements[p][n]) > Math.abs(l)) {
                    l = this.elements[p][n]
                }
            }
        }
        return l
    }, indexOf: function (l) {
        var o = null, q = this.elements.length, p, n = this.elements[0].length, m;
        for (p = 0; p < q; p++) {
            for (m = 0; m < n; m++) {
                if (this.elements[p][m] == l) {
                    return{i: p + 1, j: m + 1}
                }
            }
        }
        return null
    }, diagonal: function () {
        if (!this.isSquare) {
            return null
        }
        var m = [], o = this.elements.length;
        for (var l = 0; l < o; l++) {
            m.push(this.elements[l][l])
        }
        return i.create(m)
    }, toRightTriangular: function () {
        var u = this.dup(), o;
        var t = this.elements.length, m, l, r = this.elements[0].length, q;
        for (m = 0; m < t; m++) {
            if (u.elements[m][m] == 0) {
                for (l = m + 1; l < t; l++) {
                    if (u.elements[l][m] != 0) {
                        o = [];
                        for (q = 0; q < r; q++) {
                            o.push(u.elements[m][q] + u.elements[l][q])
                        }
                        u.elements[m] = o;
                        break
                    }
                }
            }
            if (u.elements[m][m] != 0) {
                for (l = m + 1; l < t; l++) {
                    var s = u.elements[l][m] / u.elements[m][m];
                    o = [];
                    for (q = 0; q < r; q++) {
                        o.push(q <= m ? 0 : u.elements[l][q] - u.elements[m][q] * s)
                    }
                    u.elements[l] = o
                }
            }
        }
        return u
    }, toUpperTriangular: function () {
        return this.toRightTriangular()
    }, determinant: function () {
        if (!this.isSquare()) {
            return null
        }
        var p = this.toRightTriangular();
        var m = p.elements[0][0], o = p.elements.length;
        for (var l = 1; l < o; l++) {
            m = m * p.elements[l][l]
        }
        return m
    }, det: function () {
        return this.determinant()
    }, isSingular: function () {
        return(this.isSquare() && this.determinant() === 0)
    }, trace: function () {
        if (!this.isSquare()) {
            return null
        }
        var m = this.elements[0][0], o = this.elements.length;
        for (var l = 1; l < o; l++) {
            m += this.elements[l][l]
        }
        return m
    }, tr: function () {
        return this.trace()
    }, rank: function () {
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
    }, rk: function () {
        return this.rank()
    }, augment: function (l) {
        var r = l.elements || l;
        if (typeof(r[0][0]) == "undefined") {
            r = b.create(r).elements
        }
        var o = this.dup(), q = o.elements[0].length;
        var p = o.elements.length, n = r[0].length, m;
        if (p != r.length) {
            return null
        }
        while (p--) {
            m = n;
            while (m--) {
                o.elements[p][q + m] = r[p][m]
            }
        }
        return o
    }, inverse: function () {
        if (!this.isSquare() || this.isSingular()) {
            return null
        }
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
                if (l >= o) {
                    w[t].push(q)
                }
            }
            u.elements[t] = r;
            s = t;
            while (s--) {
                r = [];
                for (l = 0; l < v; l++) {
                    r.push(u.elements[s][l] - u.elements[t][l] * u.elements[s][t])
                }
                u.elements[s] = r
            }
        }
        return b.create(w)
    }, inv: function () {
        return this.inverse()
    }, round: function () {
        return this.map(function (l) {
            return Math.round(l)
        })
    }, snapTo: function (l) {
        return this.map(function (m) {
            return(Math.abs(m - l) <= f.precision) ? l : m
        })
    }, inspect: function () {
        var m = [];
        var o = this.elements.length;
        for (var l = 0; l < o; l++) {
            m.push(i.create(this.elements[l]).inspect())
        }
        return m.join("\n")
    }, setElements: function (o) {
        var m, l, p = o.elements || o;
        if (typeof(p[0][0]) != "undefined") {
            m = p.length;
            this.elements = [];
            while (m--) {
                l = p[m].length;
                this.elements[m] = [];
                while (l--) {
                    this.elements[m][l] = p[m][l]
                }
            }
            return this
        }
        var q = p.length;
        this.elements = [];
        for (m = 0; m < q; m++) {
            this.elements.push([p[m]])
        }
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
            while (l--) {
                o[m][l] = (m == l) ? 1 : 0
            }
        }
        return b.create(o)
    };
    b.Diagonal = function (m) {
        var l = m.length;
        var n = b.I(l);
        while (l--) {
            n.elements[l][l] = m[l]
        }
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
        if (m.elements.length != 3) {
            return null
        }
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
    b.Random = function (o, l) {
        return b.Zero(o, l).map(function () {
            return Math.random()
        })
    };
    b.Zero = function (r, l) {
        var q = [], p = r, o;
        while (p--) {
            o = l;
            q[p] = [];
            while (o--) {
                q[p][o] = 0
            }
        }
        return b.create(q)
    };
    function g() {
    }

    g.prototype = {eql: function (l) {
        return(this.isParallelTo(l) && this.contains(l.anchor))
    }, dup: function () {
        return g.create(this.anchor, this.direction)
    }, translate: function (m) {
        var l = m.elements || m;
        return g.create([this.anchor.elements[0] + l[0], this.anchor.elements[1] + l[1], this.anchor.elements[2] + (l[2] || 0)], this.direction)
    }, isParallelTo: function (m) {
        if (m.normal || (m.start && m.end)) {
            return m.isParallelTo(this)
        }
        var l = this.direction.angleFrom(m.direction);
        return(Math.abs(l) <= f.precision || Math.abs(l - Math.PI) <= f.precision)
    }, distanceFrom: function (p) {
        if (p.normal || (p.start && p.end)) {
            return p.distanceFrom(this)
        }
        if (p.direction) {
            if (this.isParallelTo(p)) {
                return this.distanceFrom(p.anchor)
            }
            var t = this.direction.cross(p.direction).toUnitVector().elements;
            var n = this.anchor.elements, m = p.anchor.elements;
            return Math.abs((n[0] - m[0]) * t[0] + (n[1] - m[1]) * t[1] + (n[2] - m[2]) * t[2])
        } else {
            var q = p.elements || p;
            var n = this.anchor.elements, l = this.direction.elements;
            var w = q[0] - n[0], u = q[1] - n[1], r = (q[2] || 0) - n[2];
            var v = Math.sqrt(w * w + u * u + r * r);
            if (v === 0) {
                return 0
            }
            var s = (w * l[0] + u * l[1] + r * l[2]) / v;
            var o = 1 - s * s;
            return Math.abs(v * Math.sqrt(o < 0 ? 0 : o))
        }
    }, contains: function (l) {
        if (l.start && l.end) {
            return this.contains(l.start) && this.contains(l.end)
        }
        var m = this.distanceFrom(l);
        return(m !== null && m <= f.precision)
    }, positionOf: function (m) {
        if (!this.contains(m)) {
            return null
        }
        var n = m.elements || m;
        var l = this.anchor.elements, o = this.direction.elements;
        return(n[0] - l[0]) * o[0] + (n[1] - l[1]) * o[1] + ((n[2] || 0) - l[2]) * o[2]
    }, liesIn: function (l) {
        return l.contains(this)
    }, intersects: function (l) {
        if (l.normal) {
            return l.intersects(this)
        }
        return(!this.isParallelTo(l) && this.distanceFrom(l) <= f.precision)
    }, intersectionWith: function (w) {
        if (w.normal || (w.start && w.end)) {
            return w.intersectionWith(this)
        }
        if (!this.intersects(w)) {
            return null
        }
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
                if (this.intersects(G)) {
                    return this.intersectionWith(G)
                }
                if (this.isParallelTo(G)) {
                    return null
                }
                var I = this.direction.elements, H = G.direction.elements;
                var q = I[0], p = I[1], n = I[2], C = H[0], w = H[1], u = H[2];
                var F = (n * C - q * u), B = (q * w - p * C), v = (p * u - n * w);
                var t = [F * u - B * w, B * C - v * u, v * w - F * C];
                var s = Plane.create(G.anchor, t);
                return s.intersectionWith(this)
            } else {
                var s = G.elements || G;
                if (this.contains(s)) {
                    return i.create(s)
                }
                var J = this.anchor.elements, I = this.direction.elements;
                var q = I[0], p = I[1], n = I[2], o = J[0], m = J[1], l = J[2];
                var F = q * (s[1] - m) - p * (s[0] - o), B = p * ((s[2] || 0) - l) - n * (s[1] - m), v = n * (s[0] - o) - q * ((s[2] || 0) - l);
                var r = i.create([p * F - n * v, n * B - q * F, q * v - p * B]);
                var K = this.distanceFrom(s) / r.modulus();
                return i.create([s[0] + r.elements[0] * K, s[1] + r.elements[1] * K, (s[2] || 0) + r.elements[2] * K])
            }
        }
    }, rotate: function (F, G) {
        if (typeof(G.direction) == "undefined") {
            G = g.create(G.to3D(), i.k)
        }
        var r = b.Rotation(F, G.direction).elements;
        var m = G.pointClosestTo(this.anchor).elements;
        var o = this.anchor.elements, l = this.direction.elements;
        var v = m[0], u = m[1], s = m[2], q = o[0], p = o[1], n = o[2];
        var E = q - v, B = p - u, w = n - s;
        return g.create([v + r[0][0] * E + r[0][1] * B + r[0][2] * w, u + r[1][0] * E + r[1][1] * B + r[1][2] * w, s + r[2][0] * E + r[2][1] * B + r[2][2] * w], [r[0][0] * l[0] + r[0][1] * l[1] + r[0][2] * l[2], r[1][0] * l[0] + r[1][1] * l[1] + r[1][2] * l[2], r[2][0] * l[0] + r[2][1] * l[1] + r[2][2] * l[2]])
    }, reverse: function () {
        return g.create(this.anchor, this.direction.x(-1))
    }, reflectionIn: function (x) {
        if (x.normal) {
            var q = this.anchor.elements, m = this.direction.elements;
            var u = q[0], s = q[1], p = q[2], r = m[0], o = m[1], n = m[2];
            var l = this.anchor.reflectionIn(x).elements;
            var w = u + r, v = s + o, t = p + n;
            var y = x.pointClosestTo([w, v, t]).elements;
            var B = [y[0] + (y[0] - w) - l[0], y[1] + (y[1] - v) - l[1], y[2] + (y[2] - t) - l[2]];
            return g.create(l, B)
        } else {
            if (x.direction) {
                return this.rotate(Math.PI, x)
            } else {
                var z = x.elements || x;
                return g.create(this.anchor.reflectionIn([z[0], z[1], (z[2] || 0)]), this.direction)
            }
        }
    }, setVectors: function (l, n) {
        l = i.create(l);
        n = i.create(n);
        if (l.elements.length == 2) {
            l.elements.push(0)
        }
        if (n.elements.length == 2) {
            n.elements.push(0)
        }
        if (l.elements.length > 3 || n.elements.length > 3) {
            return null
        }
        var m = n.modulus();
        if (m === 0) {
            return null
        }
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
    h.linalg.Vector = function () {
        return i.create.apply(null, arguments)
    };
    h.linalg.Vector.create = i.create;
    h.linalg.Vector.i = i.i;
    h.linalg.Vector.j = i.j;
    h.linalg.Vector.k = i.k;
    h.linalg.Vector.Random = i.Random;
    h.linalg.Vector.Zero = i.Zero;
    h.linalg.Matrix = function () {
        return b.create.apply(null, arguments)
    };
    h.linalg.Matrix.create = b.create;
    h.linalg.Matrix.I = b.I;
    h.linalg.Matrix.Random = b.Random;
    h.linalg.Matrix.Rotation = b.Rotation;
    h.linalg.Matrix.RotationX = b.RotationX;
    h.linalg.Matrix.RotationY = b.RotationY;
    h.linalg.Matrix.RotationZ = b.RotationZ;
    h.linalg.Matrix.Zero = b.Zero;
    h.linalg.Line = function () {
        return g.create.apply(null, arguments)
    };
    h.linalg.Line.create = g.create;
    h.linalg.Line.X = g.X;
    h.linalg.Line.Y = g.Y;
    h.linalg.Line.Z = g.Z;
    h.math = {isnamespace_: true};
    if (!("toDegrees" in Number.prototype)) {
        Number.prototype.toDegrees = function () {
            return this * 180 / Math.PI
        }
    }
    if (!("toRadians" in Number.prototype)) {
        Number.prototype.toRadians = function () {
            return this * Math.PI / 180
        }
    }
    h.math.normalizeAngle = function (l) {
        l = l % (2 * Math.PI);
        return l >= 0 ? l : l + 2 * Math.PI
    };
    h.math.normalizeLat = function (l) {
        return Math.max(-90, Math.min(90, l))
    };
    h.math.normalizeLng = function (l) {
        if (l % 360 == 180) {
            return 180
        }
        l = l % 360;
        return l < -180 ? l + 360 : l > 180 ? l - 360 : l
    };
    h.math.reverseAngle = function (l) {
        return h.math.normalizeAngle(l + Math.PI)
    };
    h.math.wrapValue = function (n, l, m) {
        if (!l || !h.util.isArray(l) || l.length != 2) {
            throw new TypeError("The range parameter must be an array of 2 numbers.")
        }
        if (n === l[0]) {
            return l[0]
        }
        n -= l[0];
        n = n % (l[1] - l[0]);
        if (n < 0) {
            n += (l[1] - l[0])
        }
        n += l[0];
        return(n === l[0]) ? (m ? l[0] : l[1]) : n
    };
    h.math.constrainValue = function (m, l) {
        if (!l || !h.util.isArray(l) || l.length != 2) {
            throw new TypeError("The range parameter must be an array of 2 numbers.")
        }
        return Math.max(l[0], Math.min(l[1], m))
    };
    h.math.EARTH_RADIUS = 6378135;
    h.math.EARTH_RADIUS_CURVATURE_AVG = 6372795;
    h.math.distance = function (m, l) {
        return h.math.EARTH_RADIUS * h.math.angularDistance(m, l)
    };
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
        if (h.util.isUndefined(F) || F === null) {
            F = 0.5
        }
        if (w.equals(t)) {
            return new h.Point(w)
        }
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
        if (!("heading" in t && "distance" in t)) {
            throw new TypeError("destination() requres both heading and distance options.")
        }
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
                if (h.util.isArray(l)) {
                    m = l
                } else {
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
                            } else {
                                throw new TypeError("Could not create a point from the given Earth object")
                            }
                        }
                    } else {
                        if (a(l)) {
                            this.lat_ = l.lat();
                            this.lng_ = l.lng()
                        } else {
                            throw new TypeError("Could not create a point from the given arguments")
                        }
                    }
                }
            }
        } else {
            m = arguments
        }
        if (m) {
            for (var n = 0; n < m.length; n++) {
                if (typeof m[n] != "number") {
                    throw new TypeError("Coordinates must be numerical")
                }
            }
            this.lat_ = m[0];
            this.lng_ = m[1];
            if (m.length >= 3) {
                this.altitude_ = m[2];
                if (m.length >= 4) {
                    this.altitudeMode_ = m[3]
                }
            }
        }
        this.lat_ = h.math.normalizeLat(this.lat_);
        this.lng_ = h.math.normalizeLng(this.lng_)
    };
    h.Point.prototype.lat = function () {
        return this.lat_
    };
    h.Point.prototype.lat_ = 0;
    h.Point.prototype.lng = function () {
        return this.lng_
    };
    h.Point.prototype.lng_ = 0;
    h.Point.prototype.altitude = function () {
        return this.altitude_
    };
    h.Point.prototype.altitude_ = 0;
    h.Point.prototype.altitudeMode = function () {
        return this.altitudeMode_
    };
    h.Point.prototype.altitudeMode_ = h.ALTITUDE_RELATIVE_TO_GROUND;
    h.Point.prototype.toString = function () {
        return"(" + this.lat().toString() + ", " + this.lng().toString() + ", " + this.altitude().toString() + ")"
    };
    h.Point.prototype.flatten = function () {
        return new h.Point(this.lat(), this.lng())
    };
    h.Point.prototype.is3D = function () {
        return this.altitude_ !== 0
    };
    h.Point.prototype.equals = function (l) {
        return this.lat() == l.lat() && this.lng() == l.lng() && this.altitude() == l.altitude() && this.altitudeMode() == l.altitudeMode()
    };
    h.Point.prototype.angularDistance = function (l) {
        return h.math.angularDistance(this, l)
    };
    h.Point.prototype.distance = function (l) {
        return h.math.distance(this, l)
    };
    h.Point.prototype.heading = function (l) {
        return h.math.heading(this, l)
    };
    h.Point.prototype.midpoint = function (l, m) {
        return h.math.midpoint(this, l, m)
    };
    h.Point.prototype.destination = function (l) {
        return h.math.destination(this, l)
    };
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
        if (q > 90) {
            q -= 180
        }
        var o = 0;
        if (Math.abs(q) < 90) {
            o = -Math.atan2(m.e(3), m.e(1)).toDegrees()
        }
        return new h.Point(q, o, l)
    };
    h.Bounds = function () {
        if (arguments.length == 1) {
            if (arguments[0].constructor === h.Bounds) {
                var m = arguments[0];
                this.sw_ = new h.Point(m.southWestBottom());
                this.ne_ = new h.Point(m.northEastTop())
            } else {
                this.sw_ = this.ne_ = new h.Point(arguments[0])
            }
        } else {
            if (arguments.length == 2) {
                var l = new h.Point(arguments[0]);
                var n = new h.Point(arguments[1]);
                if (!l && !n) {
                    return
                } else {
                    if (!l) {
                        l = n
                    } else {
                        if (!n) {
                            n = l
                        }
                    }
                }
                if (l.lat() > n.lat()) {
                    throw new RangeError("Bounds southwest coordinate cannot be north of the northeast coordinate")
                }
                if (l.altitude() > n.altitude()) {
                    throw new RangeError("Bounds southwest coordinate cannot be north of the northeast coordinate")
                }
                this.sw_ = l;
                this.ne_ = n
            }
        }
    };
    h.Bounds.prototype.southWestBottom = function () {
        return this.sw_
    };
    h.Bounds.prototype.sw_ = null;
    h.Bounds.prototype.south = function () {
        return !this.isEmpty() ? this.sw_.lat() : null
    };
    h.Bounds.prototype.west = function () {
        return !this.isEmpty() ? this.sw_.lng() : null
    };
    h.Bounds.prototype.bottom = function () {
        return !this.isEmpty() ? this.sw_.altitude() : null
    };
    h.Bounds.prototype.northEastTop = function () {
        return this.ne_
    };
    h.Bounds.prototype.ne_ = null;
    h.Bounds.prototype.north = function () {
        return !this.isEmpty() ? this.ne_.lat() : null
    };
    h.Bounds.prototype.east = function () {
        return !this.isEmpty() ? this.ne_.lng() : null
    };
    h.Bounds.prototype.top = function () {
        return !this.isEmpty() ? this.ne_.altitude() : null
    };
    h.Bounds.prototype.crossesAntimeridian = function () {
        return !this.isEmpty() && (this.sw_.lng() > this.ne_.lng())
    };
    h.Bounds.prototype.is3D = function () {
        return !this.isEmpty() && (this.sw_.is3D() || this.ne_.is3D())
    };
    h.Bounds.prototype.containsPoint = function (l) {
        l = new h.Point(l);
        if (this.isEmpty()) {
            return false
        }
        if (!(this.south() <= l.lat() && l.lat() <= this.north())) {
            return false
        }
        if (this.is3D() && !(this.bottom() <= l.altitude() && l.altitude() <= this.top())) {
            return false
        }
        return this.containsLng_(l.lng())
    };
    h.Bounds.prototype.containsLng_ = function (l) {
        if (this.crossesAntimeridian()) {
            return(l <= this.east() || l >= this.west())
        } else {
            return(this.west() <= l && l <= this.east())
        }
    };
    function c(l, m) {
        return(l > m) ? (m + 360 - l) : (m - l)
    }

    h.Bounds.prototype.extend = function (s) {
        s = new h.Point(s);
        if (this.containsPoint(s)) {
            return
        }
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
            if (r <= o) {
                m = s.lng()
            } else {
                n = s.lng()
            }
        }
        this.sw_ = new h.Point(t, n, l);
        this.ne_ = new h.Point(p, m, q)
    };
    h.Bounds.prototype.span = function () {
        if (this.isEmpty()) {
            return{lat: 0, lng: 0, altitude: 0}
        }
        return{lat: (this.ne_.lat() - this.sw_.lat()), lng: c(this.sw_.lng(), this.ne_.lng()), altitude: this.is3D() ? (this.ne_.altitude() - this.sw_.altitude()) : null}
    };
    h.Bounds.prototype.isEmpty = function () {
        return(this.sw_ === null && this.sw_ === null)
    };
    h.Bounds.prototype.center = function () {
        if (this.isEmpty()) {
            return null
        }
        return new h.Point((this.sw_.lat() + this.ne_.lat()) / 2, this.crossesAntimeridian() ? h.math.normalizeLng(this.sw_.lng() + c(this.sw_.lng(), this.ne_.lng()) / 2) : (this.sw_.lng() + this.ne_.lng()) / 2, (this.sw_.altitude() + this.ne_.altitude()) / 2)
    };
    h.Bounds.prototype.getCenter = h.Bounds.prototype.center;
    h.Bounds.prototype.isFullLat = function () {
        return !this.isEmpty() && (this.south() == -90 && this.north() == 90)
    };
    h.Bounds.prototype.isFullLng = function () {
        return !this.isEmpty() && (this.west() == -180 && this.east() == 180)
    };
    h.Path = function () {
        this.coords_ = [];
        var l = null;
        var m, q;
        if (arguments.length == 1) {
            var p = arguments[0];
            if (p.constructor === h.Path) {
                for (m = 0; m < p.numCoords(); m++) {
                    this.coords_.push(new h.Point(p.coord(m)))
                }
            } else {
                if (h.util.isArray(p)) {
                    l = p
                } else {
                    if (j(p)) {
                        var o = p.getType();
                        if (o == "KmlLineString" || o == "KmlLinearRing") {
                            q = p.getCoordinates().getLength();
                            for (m = 0; m < q; m++) {
                                this.coords_.push(new h.Point(p.getCoordinates().get(m)))
                            }
                        } else {
                            throw new TypeError("Could not create a path from the given arguments")
                        }
                    } else {
                        if ("getVertex" in p && "getVertexCount" in p) {
                            q = p.getVertexCount();
                            for (m = 0; m < q; m++) {
                                this.coords_.push(new h.Point(p.getVertex(m)))
                            }
                        } else {
                            throw new TypeError("Could not create a path from the given arguments")
                        }
                    }
                }
            }
        } else {
            l = arguments
        }
        if (l) {
            for (m = 0; m < l.length; m++) {
                this.coords_.push(new h.Point(l[m]))
            }
        }
    };
    h.Path.prototype.coords_ = null;
    h.Path.prototype.toString = function () {
        return"[" + this.coords_.map(function (l) {
            return l.toString()
        }).join(", ") + "]"
    };
    h.Path.prototype.equals = function (m) {
        for (var l = 0; l < m.numCoords(); l++) {
            if (!this.coord(l).equals(m.coord(l))) {
                return false
            }
        }
        return true
    };
    h.Path.prototype.numCoords = function () {
        return this.coords_.length
    };
    h.Path.prototype.coord = function (l) {
        return this.coords_[l]
    };
    h.Path.prototype.prepend = function (l) {
        this.coords_.unshift(new h.Point(l))
    };
    h.Path.prototype.append = function (l) {
        this.coords_.push(new h.Point(l))
    };
    h.Path.prototype.insert = function (l, m) {
        this.coords_.splice(l, 0, new h.Point(m))
    };
    h.Path.prototype.remove = function (l) {
        this.coords_.splice(l, 1)
    };
    h.Path.prototype.subPath = function (m, l) {
        return this.coords_.slice(m, l)
    };
    h.Path.prototype.reverse = function () {
        this.coords_.reverse()
    };
    h.Path.prototype.distance = function () {
        var m = 0;
        for (var l = 0; l < this.coords_.length - 1; l++) {
            m += this.coords_[l].distance(this.coords_[l + 1])
        }
        return m
    };
    h.Path.prototype.containsPoint = function (m) {
        var p = false;
        var q = m.lat();
        var l = m.lng();
        for (var o = 0; o < this.coords_.length; o++) {
            var n = (o + 1) % this.coords_.length;
            if (((this.coords_[o].lat() < q && this.coords_[n].lat() >= q) || (this.coords_[n].lat() < q && this.coords_[o].lat() >= q)) && (this.coords_[o].lng() + (q - this.coords_[o].lat()) / (this.coords_[n].lat() - this.coords_[o].lat()) * (this.coords_[n].lng() - this.coords_[o].lng()) < l)) {
                p = !p
            }
        }
        return p
    };
    h.Path.prototype.bounds = function () {
        if (!this.numCoords()) {
            return new h.Bounds()
        }
        var m = new h.Bounds(this.coord(0));
        var n = this.numCoords();
        for (var l = 1; l < n; l++) {
            m.extend(this.coord(l))
        }
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
    h.Path.prototype.area = function () {
        return Math.abs(this.signedArea_())
    };
    h.Path.prototype.isCounterClockwise_ = function () {
        return Boolean(this.signedArea_() >= 0)
    };
    h.Polygon = function () {
        this.outerBoundary_ = new h.Path();
        this.innerBoundaries_ = [];
        var m;
        if (arguments.length === 0) {
        } else {
            if (arguments.length == 1) {
                var p = arguments[0];
                if (p.constructor === h.Polygon) {
                    this.outerBoundary_ = new h.Path(p.outerBoundary());
                    for (m = 0; m < p.innerBoundaries().length; m++) {
                        this.innerBoundaries_.push(new h.Path(p.innerBoundaries()[m]))
                    }
                } else {
                    if (j(p)) {
                        var o = p.getType();
                        if (o == "KmlLineString" || o == "KmlLinearRing") {
                            this.outerBoundary_ = new h.Path(p)
                        } else {
                            if (o == "KmlPolygon") {
                                this.outerBoundary_ = new h.Path(p.getOuterBoundary());
                                var l = p.getInnerBoundaries().getChildNodes();
                                var q = l.getLength();
                                for (m = 0; m < q; m++) {
                                    this.innerBoundaries_.push(new h.Path(l.item(m)))
                                }
                            } else {
                                throw new TypeError("Could not create a polygon from the given arguments")
                            }
                        }
                    } else {
                        this.outerBoundary_ = new h.Path(arguments[0])
                    }
                }
            } else {
                if (arguments[0].length && typeof arguments[0][0] == "number") {
                    this.outerBoundary_ = new h.Path(arguments)
                } else {
                    if (arguments[1]) {
                        this.outerBoundary_ = new h.Path(arguments[0]);
                        if (!h.util.isArray(arguments[1])) {
                            throw new TypeError("Second argument to geo.Polygon constructor must be an array of paths.")
                        }
                        for (m = 0; m < arguments[1].length; m++) {
                            this.innerBoundaries_.push(new h.Path(arguments[1][m]))
                        }
                    } else {
                        throw new TypeError("Cannot create a path from the given arguments.")
                    }
                }
            }
        }
    };
    h.Polygon.prototype.outerBoundary_ = null;
    h.Polygon.prototype.innerBoundaries_ = null;
    h.Polygon.prototype.toString = function () {
        return"Polygon: " + this.outerBoundary().toString() + (this.innerBoundaries().length ? ", (" + this.innerBoundaries().length + " inner boundaries)" : "")
    };
    h.Polygon.prototype.outerBoundary = function () {
        return this.outerBoundary_
    };
    h.Polygon.prototype.innerBoundaries = function () {
        return this.innerBoundaries_
    };
    h.Polygon.prototype.containsPoint = function (l) {
        if (!this.outerBoundary_.containsPoint(l)) {
            return false
        }
        for (var m = 0; m < this.innerBoundaries_.length; m++) {
            if (this.innerBoundaries_[m].containsPoint(l)) {
                return false
            }
        }
        return true
    };
    h.Polygon.prototype.bounds = function () {
        return this.outerBoundary_.bounds()
    };
    h.Polygon.prototype.area = function () {
        var m = this.outerBoundary_.area();
        for (var l = 0; l < this.innerBoundaries_.length; l++) {
            m -= this.innerBoundaries_[l].area()
        }
        return m
    };
    h.Polygon.prototype.isCounterClockwise = function () {
        return this.outerBoundary_.isCounterClockwise_()
    };
    h.Polygon.prototype.makeCounterClockwise = function () {
        if (this.isCounterClockwise()) {
            this.outerBoundary_.reverse()
        }
    };
    h.util = {isnamespace_: true};
    h.util.isUndefined = function (l) {
        return typeof l == "undefined"
    };
    h.util.isArray = function (l) {
        return l !== null && typeof l == "object" && "splice" in l && "join" in l
    };
    h.util.isFunction = function (l) {
        return l !== null && typeof l == "function" && "call" in l && "apply" in l
    };
    function j(l) {
        return l !== null && (typeof l == "function" || typeof l == "object") && "getType" in l
    }

    h.util.isObjectLiteral = function (l) {
        return l !== null && typeof l == "object" && l.constructor === Object && !j(l)
    };
    function a(l) {
        return(window.google && window.google.maps && window.google.maps.LatLng && l.constructor === window.google.maps.LatLng)
    }

    window.geo = h
})();
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
    var v = function (i) {
        var F = this;
        this.pluginInstance = i;
        function E(H) {
            return function () {
                return H.apply(F, arguments)
            }
        }

        function G(H) {
            for (var I in H) {
                var L = H[I];
                if (geo.util.isFunction(L)) {
                    if (L.isclass_) {
                        L.extInstance_ = F
                    } else {
                        H[I] = E(L)
                    }
                }
                if (c(L)) {
                    var K = {};
                    for (var J in L) {
                        K[J] = L[J]
                    }
                    G(K);
                    H[I] = K
                }
            }
        }

        G(this)
    };
    var h = Infinity;
    var n = null;
    var d = undefined;

    function t(E, J, F) {
        var H = {};
        E = E || {};
        F = F || {};
        for (var I in E) {
            if (!J && !(I in F)) {
                var G = [];
                for (var i in F) {
                    G.push(i)
                }
                throw new Error("Unexpected parameter '" + I + "'. Allowed parameters are: " + G.join(", ") + ".")
            }
            H[I] = E[I]
        }
        for (I in F) {
            if (!(I in H)) {
                if (F[I] === d) {
                    throw new Error("Required parameter '" + I + "' was not passed.")
                }
                if (F[I] != n && F[I] != h) {
                    H[I] = F[I]
                }
            }
        }
        return H
    }

    function s() {
        var E = [];
        var G = null;
        if (geo.util.isArray(arguments[0])) {
            E = arguments[0];
            G = arguments[1]
        } else {
            G = arguments[0]
        }
        G.isclass_ = true;
        for (var H = 0; H < E.length; H++) {
            for (var F in E[H].prototype) {
                G.prototype[F] = E[H].prototype[F]
            }
        }
        return G
    }

    function c(i) {
        return i !== null && typeof i == "object" && "isnamespace_" in i && i.isnamespace_
    }

    v.isInstanceOfEarthInterface = function (i, E) {
        return i !== null && (typeof i == "object" || typeof i == "function") && "getType" in i && i.getType() == E
    };
    v.prototype.dom = {isnamespace_: true};
    function g(G) {
        if (G.apiInterface && !geo.util.isArray(G.apiInterface)) {
            G.apiInterface = [G.apiInterface]
        }
        var E = G.base;
        while (E) {
            if ("propertySpec" in E.builderParams) {
                if (!("propertySpec" in G)) {
                    G.propertySpec = []
                }
                for (var H in E.builderParams.propertySpec) {
                    if (!(H in G.propertySpec)) {
                        G.propertySpec[H] = E.builderParams.propertySpec[H]
                    }
                }
            }
            if (!G.apiInterface) {
                G.apiInterface = E.builderParams.apiInterface
            }
            if (!G.apiFactoryFn) {
                G.apiFactoryFn = E.builderParams.apiFactoryFn
            }
            E = E.builderParams.base
        }
        var F = {id: ""};
        for (H in F) {
            if (!(H in G.propertySpec)) {
                G.propertySpec[H] = F[H]
            }
        }
        var i = function () {
            var K = {};
            var L;
            if (arguments.length === 0) {
                throw new TypeError("Cannot create object without any arguments!")
            } else {
                if (arguments.length == 1) {
                    for (L = 0; L < G.apiInterface.length; L++) {
                        if (v.isInstanceOfEarthInterface(arguments[0], G.apiInterface[L])) {
                            return arguments[0]
                        }
                    }
                    var I = arguments[0];
                    if (geo.util.isObjectLiteral(I)) {
                        K = I
                    } else {
                        if ("defaultProperty" in G) {
                            K[G.defaultProperty] = I
                        } else {
                            throw new TypeError("Expected options object")
                        }
                    }
                } else {
                    if (arguments.length == 2) {
                        if ("defaultProperty" in G) {
                            K = arguments[1];
                            K[G.defaultProperty] = arguments[0]
                        } else {
                            throw new Error("No default property for the DOM builder")
                        }
                    }
                }
            }
            K = t(K, false, G.propertySpec);
            var J = this.util.callMethod(this.pluginInstance, G.apiFactoryFn, K.id);
            if (!geo.util.isUndefined(G.constructor)) {
                G.constructor.call(this, J, K)
            }
            E = G.base;
            while (E) {
                if ("constructor" in E.builderParams) {
                    E.builderParams.constructor.call(this, J, K)
                }
                E = E.builderParams.base
            }
            for (var M in G.propertySpec) {
                if (G.propertySpec[M] === h && M in K) {
                    this.util.callMethod(J, "set" + M.charAt(0).toUpperCase() + M.substr(1), K[M])
                }
            }
            return J
        };
        i.builderParams = G;
        return i
    }

    v.prototype.dom.buildFeature_ = g({propertySpec: {name: h, visibility: h, description: h, snippet: h, region: n}, constructor: function (E, i) {
        if (i.region) {
            E.setRegion(this.dom.buildRegion(i.region))
        }
    }});
    v.prototype.dom.buildPlacemark = g({apiInterface: "KmlPlacemark", base: v.prototype.dom.buildFeature_, apiFactoryFn: "createPlacemark", propertySpec: {point: n, lineString: n, linearRing: n, polygon: n, model: n, geometries: n, altitudeMode: n, stockIcon: n, icon: n, style: n, highlightStyle: n}, constructor: function (G, i) {
        var E = [];
        if (i.point) {
            E.push(this.dom.buildPoint(i.point))
        }
        if (i.lineString) {
            E.push(this.dom.buildLineString(i.lineString))
        }
        if (i.linearRing) {
            E.push(this.dom.buildLinearRing(i.linearRing))
        }
        if (i.polygon) {
            E.push(this.dom.buildPolygon(i.polygon))
        }
        if (i.model) {
            E.push(this.dom.buildModel(i.model))
        }
        if (i.multiGeometry) {
            E.push(this.dom.buildMultiGeometry(i.multiGeometry))
        }
        if (i.geometries) {
            E = E.concat(i.geometries)
        }
        if (E.length > 1) {
            G.setGeometry(this.dom.buildMultiGeometry(E))
        } else {
            if (E.length == 1) {
                G.setGeometry(E[0])
            }
        }
        if (i.stockIcon) {
            i.icon = i.icon || {};
            i.icon.stockIcon = i.stockIcon
        }
        if (i.icon) {
            if (!i.style) {
                i.style = {}
            }
            i.style.icon = i.icon
        }
        if ("altitudeMode" in i) {
            G.getGeometry().setAltitudeMode(i.altitudeMode)
        }
        if (i.style) {
            if (i.highlightStyle) {
                var F = this.pluginInstance.createStyleMap("");
                if (typeof i.style == "string") {
                    F.setNormalStyleUrl(i.style)
                } else {
                    F.setNormalStyle(this.dom.buildStyle(i.style))
                }
                if (typeof i.highlightStyle == "string") {
                    F.setHighlightStyleUrl(i.highlightStyle)
                } else {
                    F.setHighlightStyle(this.dom.buildStyle(i.highlightStyle))
                }
                G.setStyleSelector(F)
            } else {
                if (typeof i.style == "string") {
                    G.setStyleUrl(i.style)
                } else {
                    G.setStyleSelector(this.dom.buildStyle(i.style))
                }
            }
        }
    }});
    v.prototype.dom.buildPointPlacemark = g({base: v.prototype.dom.buildPlacemark, defaultProperty: "point"});
    v.prototype.dom.buildLineStringPlacemark = g({base: v.prototype.dom.buildPlacemark, defaultProperty: "lineString"});
    v.prototype.dom.buildPolygonPlacemark = g({base: v.prototype.dom.buildPlacemark, defaultProperty: "polygon"});
    v.prototype.dom.buildNetworkLink = g({apiInterface: "KmlNetworkLink", base: v.prototype.dom.buildFeature_, apiFactoryFn: "createNetworkLink", defaultProperty: "link", propertySpec: {link: n, flyToView: h, refreshVisibility: h}, constructor: function (E, i) {
        if (i.link) {
            E.setLink(this.dom.buildLink(i.link))
        }
    }});
    v.prototype.dom.buildContainer_ = g({base: v.prototype.dom.buildFeature_, propertySpec: {children: n}, constructor: function (G, E) {
        if (E.children) {
            for (var F = 0; F < E.children.length; F++) {
                G.getFeatures().appendChild(E.children[F])
            }
        }
    }});
    v.prototype.dom.buildFolder = g({apiInterface: "KmlFolder", base: v.prototype.dom.buildContainer_, apiFactoryFn: "createFolder", defaultProperty: "children"});
    v.prototype.dom.buildDocument = g({apiInterface: "KmlDocument", base: v.prototype.dom.buildContainer_, apiFactoryFn: "createDocument", defaultProperty: "children"});
    v.prototype.dom.buildOverlay_ = g({base: v.prototype.dom.buildFeature_, propertySpec: {color: n, icon: n, drawOrder: h}, constructor: function (F, i) {
        if (i.color) {
            F.getColor().set(this.util.parseColor(i.color))
        }
        if (i.icon) {
            var E = this.pluginInstance.createIcon("");
            F.setIcon(E);
            if (typeof i.icon == "string") {
                E.setHref(i.icon)
            }
        }
    }});
    v.prototype.dom.buildGroundOverlay = g({apiInterface: "KmlGroundOverlay", base: v.prototype.dom.buildOverlay_, apiFactoryFn: "createGroundOverlay", defaultProperty: "icon", propertySpec: {box: d, altitude: h, altitudeMode: h}, constructor: function (i, E) {
        if (E.box) {
            var F = this.pluginInstance.createLatLonBox("");
            F.setBox(E.box.north, E.box.south, E.box.east, E.box.west, E.box.rotation ? E.box.rotation : 0);
            i.setLatLonBox(F)
        }
    }});
    v.prototype.dom.buildScreenOverlay = g({apiInterface: "KmlScreenOverlay", base: v.prototype.dom.buildOverlay_, apiFactoryFn: "createScreenOverlay", defaultProperty: "icon", propertySpec: {screenXY: d, size: d, rotation: h, overlayXY: {left: 0, top: 0}, rotationXY: n}, constructor: function (i, E) {
        this.dom.setVec2(i.getScreenXY(), E.overlayXY);
        this.dom.setVec2(i.getOverlayXY(), E.screenXY);
        this.dom.setVec2(i.getSize(), E.size);
        if ("rotationXY" in E) {
            this.dom.setVec2(i.getRotationXY(), E.rotationXY)
        }
    }});
    var z = ["Placemark", "PointPlacemark", "LineStringPlacemark", "PolygonPlacemark", "Folder", "NetworkLink", "GroundOverlay", "ScreenOverlay", "Style"];
    for (var y = 0; y < z.length; y++) {
        v.prototype.dom["add" + z[y]] = function (i) {
            return function () {
                var E = this.dom["build" + i].apply(null, arguments);
                this.pluginInstance.getFeatures().appendChild(E);
                return E
            }
        }(z[y])
    }
    v.prototype.dom.buildExtrudableGeometry_ = g({propertySpec: {altitudeMode: h, extrude: h, tessellate: h}});
    v.prototype.dom.buildPoint = g({apiInterface: "KmlPoint", base: v.prototype.dom.buildExtrudableGeometry_, apiFactoryFn: "createPoint", defaultProperty: "point", propertySpec: {point: d}, constructor: function (E, F) {
        var i = new geo.Point(F.point);
        E.set(i.lat(), i.lng(), i.altitude(), ("altitudeMode" in F) ? F.altitudeMode : i.altitudeMode(), false, false)
    }});
    v.prototype.dom.buildLineString = g({apiInterface: "KmlLineString", base: v.prototype.dom.buildExtrudableGeometry_, apiFactoryFn: "createLineString", defaultProperty: "path", propertySpec: {path: d}, constructor: function (G, F) {
        var E = G.getCoordinates();
        var J = new geo.Path(F.path);
        var I = J.numCoords();
        for (var H = 0; H < I; H++) {
            E.pushLatLngAlt(J.coord(H).lat(), J.coord(H).lng(), J.coord(H).altitude())
        }
    }});
    v.prototype.dom.buildLinearRing = g({apiInterface: "KmlLinearRing", base: v.prototype.dom.buildLineString, apiFactoryFn: "createLinearRing", defaultProperty: "path", constructor: function (i, E) {
    }});
    v.prototype.dom.buildPolygon = g({apiInterface: "KmlPolygon", base: v.prototype.dom.buildExtrudableGeometry_, apiFactoryFn: "createPolygon", defaultProperty: "polygon", propertySpec: {polygon: d}, constructor: function (I, F) {
        var H = new geo.Polygon(F.polygon);
        I.setOuterBoundary(this.dom.buildLinearRing(H.outerBoundary()));
        if (H.innerBoundaries().length) {
            var E = H.innerBoundaries();
            for (var G = 0; G < E.length; G++) {
                I.getInnerBoundaries().appendChild(this.dom.buildLinearRing(E[G]))
            }
        }
    }});
    v.prototype.dom.buildModel = g({apiInterface: "KmlModel", apiFactoryFn: "createModel", defaultProperty: "link", propertySpec: {altitudeMode: h, link: n, location: n, scale: n, orientation: n}, constructor: function (I, E) {
        if (E.link) {
            I.setLink(this.dom.buildLink(E.link))
        }
        if (E.location) {
            var i = new geo.Point(E.location);
            var H = this.pluginInstance.createLocation("");
            H.setLatLngAlt(i.lat(), i.lng(), i.altitude());
            I.setLocation(H);
            I.setAltitudeMode(i.altitudeMode())
        }
        if (E.scale) {
            var F = this.pluginInstance.createScale("");
            if (typeof E.scale == "number") {
                F.set(E.scale, E.scale, E.scale)
            } else {
                if (geo.util.isArray(E.scale)) {
                    F.set(E.scale[0], E.scale[1], E.scale[2])
                }
            }
            I.setScale(F)
        }
        if (E.orientation) {
            var G = this.pluginInstance.createOrientation("");
            if ("heading" in E.orientation && "tilt" in E.orientation && "roll" in E.orientation) {
                G.set(E.orientation.heading, E.orientation.tilt, E.orientation.roll)
            }
            I.setOrientation(G)
        }
    }});
    v.prototype.dom.buildMultiGeometry = g({apiInterface: "KmlMultiGeometry", apiFactoryFn: "createMultiGeometry", defaultProperty: "geometries", propertySpec: {geometries: n}, constructor: function (F, G) {
        var E = F.getGeometries();
        if (geo.util.isArray(G.geometries)) {
            for (var H = 0; H < G.geometries.length; H++) {
                E.appendChild(G.geometries[H])
            }
        }
    }});
    v.prototype.dom.buildLink = g({apiInterface: "KmlLink", apiFactoryFn: "createLink", defaultProperty: "href", propertySpec: {href: h, refreshMode: h, refreshInterval: h, viewRefreshMode: h, viewBoundScale: h}});
    v.prototype.dom.buildRegion = g({apiInterface: "KmlRegion", apiFactoryFn: "createRegion", propertySpec: {box: d, lod: n}, constructor: function (G, E) {
        var F = this.pluginInstance.createLatLonAltBox("");
        if (E.box.center && E.box.span) {
            if (!geo.util.isArray(E.box.span) && typeof E.box.span === "number") {
                E.box.span = [E.box.span, E.box.span]
            }
            var i = new geo.Point(E.box.center);
            E.box.north = i.lat() + E.box.span[0] / 2;
            E.box.south = i.lat() - E.box.span[0] / 2;
            E.box.east = i.lng() + E.box.span[1] / 2;
            E.box.west = i.lng() - E.box.span[1] / 2
        }
        F.setAltBox(E.box.north, E.box.south, E.box.east, E.box.west, E.box.rotation || 0, E.box.minAltitude || 0, E.box.maxAltitude || 0, E.box.altitudeMode || this.pluginInstance.ALTITUDE_CLAMP_TO_GROUND);
        var H = this.pluginInstance.createLod("");
        H.set(-1, -1, 0, 0);
        if (E.lod && geo.util.isArray(E.lod)) {
            if (E.lod.length == 2) {
                H.set(E.lod[0], E.lod[1], 0, 0)
            } else {
                if (E.lod.length == 4) {
                    H.set(E.lod[0], E.lod[3], E.lod[1], E.lod[2])
                } else {
                }
            }
        }
        G.setLatLonAltBox(F);
        G.setLod(H)
    }});
    v.prototype.dom.buildStyle = g({apiInterface: ["KmlStyle", "KmlStyleMap"], apiFactoryFn: "createStyle", propertySpec: {icon: n, label: n, line: n, poly: n, balloon: n}, constructor: function (i, N) {
        var M = function (O) {
            return((O.length < 2) ? "0" : "") + O
        };
        var K = this;
        var E = function (O, P) {
            O = O ? K.util.parseColor(O) : "ffffffff";
            if (!geo.util.isUndefined(P)) {
                O = M(Math.floor(255 * P).toString(16)) + O.substring(2)
            }
            return O
        };
        if (N.icon) {
            var I = i.getIconStyle();
            if (typeof N.icon == "string") {
                N.icon = {href: N.icon}
            }
            var L = this.pluginInstance.createIcon("");
            I.setIcon(L);
            if ("href" in N.icon) {
                L.setHref(N.icon.href)
            } else {
                if ("stockIcon" in N.icon) {
                    L.setHref("http://maps.google.com/mapfiles/kml/" + N.icon.stockIcon + ".png")
                } else {
                    L.setHref("http://maps.google.com/mapfiles/kml/paddle/wht-blank.png");
                    I.getHotSpot().set(0.5, this.pluginInstance.UNITS_FRACTION, 0, this.pluginInstance.UNITS_FRACTION)
                }
            }
            if ("scale" in N.icon) {
                I.setScale(N.icon.scale)
            }
            if ("heading" in N.icon) {
                I.setHeading(N.icon.heading)
            }
            if ("color" in N.icon || "opacity" in N.icon) {
                N.icon.color = E(N.icon.color, N.icon.opacity);
                I.getColor().set(N.icon.color)
            }
            if ("opacity" in N.icon) {
                if (!("color" in N.icon)) {
                    N.icon.color = "ffffffff"
                }
                N.icon.color = M(N.icon.opacity.toString(16)) + N.icon.color.substring(2)
            }
            if ("hotSpot" in N.icon) {
                this.dom.setVec2(I.getHotSpot(), N.icon.hotSpot)
            }
        }
        if (N.label) {
            var J = i.getLabelStyle();
            if (typeof N.label == "string") {
                N.label = {color: N.label}
            }
            if ("scale" in N.label) {
                J.setScale(N.label.scale)
            }
            if ("color" in N.label || "opacity" in N.label) {
                N.label.color = E(N.label.color, N.label.opacity);
                J.getColor().set(N.label.color)
            }
        }
        if (N.line) {
            var H = i.getLineStyle();
            if (typeof N.line == "string") {
                N.line = {color: N.line}
            }
            if ("width" in N.line) {
                H.setWidth(N.line.width)
            }
            if ("color" in N.line || "opacity" in N.line) {
                N.line.color = E(N.line.color, N.line.opacity);
                H.getColor().set(N.line.color)
            }
        }
        if (N.poly) {
            var F = i.getPolyStyle();
            if (typeof N.poly == "string") {
                N.poly = {color: N.poly}
            }
            if ("fill" in N.poly) {
                F.setFill(N.poly.fill)
            }
            if ("outline" in N.poly) {
                F.setOutline(N.poly.outline)
            }
            if ("color" in N.poly || "opacity" in N.poly) {
                N.poly.color = E(N.poly.color, N.poly.opacity);
                F.getColor().set(N.poly.color)
            }
        }
        if (N.balloon) {
            var G = i.getBalloonStyle();
            if (typeof N.balloon == "string") {
                N.balloon = {bgColor: N.balloon}
            }
            if ("bgColor" in N.balloon) {
                G.getBgColor().set(K.util.parseColor(N.balloon.bgColor))
            }
            if ("textColor" in N.balloon) {
                G.getTextColor().set(K.util.parseColor(N.balloon.textColor))
            }
            if ("text" in N.balloon) {
                G.setText(N.balloon.text)
            }
        }
    }});
    v.prototype.dom.clearFeatures = function () {
        var i = this.pluginInstance.getFeatures();
        var E;
        while ((E = i.getLastChild()) !== null) {
            i.removeChild(E)
        }
    };
    v.prototype.dom.walk = function () {
        var i;
        if (arguments.length == 1) {
            if (geo.util.isObjectLiteral(arguments[0])) {
                i = arguments[0]
            } else {
                if (geo.util.isFunction(arguments[0])) {
                    i = {visitCallback: arguments[0]}
                } else {
                    throw new TypeError("walk requires a visit callback function or options literal as a first parameter")
                }
            }
        } else {
            throw new Error("walk takes at most 1 arguments")
        }
        i = t(i, false, {visitCallback: d, features: true, geometries: false, rootObject: this.pluginInstance, rootContext: n});
        var E = function (H, G) {
            var M = {current: G, child: G, walkChildren: true};
            var I = i.visitCallback.call(H, M);
            if (!I && !geo.util.isUndefined(I)) {
                return false
            }
            if (!M.walkChildren) {
                return true
            }
            var L = null;
            if ("getFeatures" in H) {
                if (i.features) {
                    L = H.getFeatures()
                }
            } else {
                if ("getGeometry" in H) {
                    if (i.geometries && H.getGeometry()) {
                        E(H.getGeometry(), M.child)
                    }
                } else {
                    if ("getGeometries" in H) {
                        if (i.geometries) {
                            L = H.getGeometries()
                        }
                    } else {
                        if ("getOuterBoundary" in H) {
                            if (i.geometries && H.getOuterBoundary()) {
                                E(H.getOuterBoundary(), M.child);
                                L = H.getInnerBoundaries()
                            }
                        }
                    }
                }
            }
            if (L && L.hasChildNodes()) {
                var N = L.getChildNodes();
                var K = N.getLength();
                for (var J = 0; J < K; J++) {
                    var F = N.item(J);
                    if (!E(F, M.child)) {
                        return false
                    }
                }
            }
            return true
        };
        if (i.rootObject) {
            E(i.rootObject, i.rootContext)
        }
    };
    v.prototype.dom.getObjectById = function (F, E) {
        E = t(E, false, {recursive: true, rootObject: this.pluginInstance});
        if ("getId" in E.rootObject && E.rootObject.getId() == F) {
            return E.rootObject
        }
        var i = null;
        this.dom.walk({rootObject: E.rootObject, features: true, geometries: true, visitCallback: function () {
            if ("getId" in this && this.getId() == F) {
                i = this;
                return false
            }
        }});
        return i
    };
    v.prototype.dom.removeObject = function (i) {
        if (!i) {
            return
        }
        var E = i.getParentNode();
        if (!E) {
            throw new Error("Cannot remove an object without a parent.")
        }
        var F = null;
        if ("getFeatures" in E) {
            F = E.getFeatures()
        } else {
            if ("getGeometries" in E) {
                F = E.getGeometries()
            } else {
                if ("getInnerBoundaries" in E) {
                    F = E.getInnerBoundaries()
                }
            }
        }
        F.removeChild(i)
    };
    v.prototype.dom.setVec2 = function (G, F) {
        if ("getType" in F && F.getType() == "KmlVec2") {
            G.set(F.getX(), F.getXUnits(), F.getY(), F.getYUnits());
            return
        }
        F = t(F, false, {left: n, top: n, right: n, bottom: n, width: n, height: n});
        if ("width" in F) {
            F.left = F.width
        }
        if ("height" in F) {
            F.bottom = F.height
        }
        var E = 0;
        var i = this.pluginInstance.UNITS_PIXELS;
        var I = 0;
        var H = this.pluginInstance.UNITS_PIXELS;
        if ("left" in F) {
            if (typeof F.left == "number") {
                E = F.left
            } else {
                if (typeof F.left == "string" && F.left.charAt(F.left.length - 1) == "%") {
                    E = parseFloat(F.left) / 100;
                    i = this.pluginInstance.UNITS_FRACTION
                } else {
                    throw new TypeError("left must be a number or string indicating a percentage")
                }
            }
        } else {
            if ("right" in F) {
                if (typeof F.right == "number") {
                    E = F.right;
                    i = this.pluginInstance.UNITS_INSET_PIXELS
                } else {
                    if (typeof F.right == "string" && F.right.charAt(F.right.length - 1) == "%") {
                        E = 1 - parseFloat(F.right) / 100;
                        i = this.pluginInstance.UNITS_FRACTION
                    } else {
                        throw new TypeError("right must be a number or string indicating a percentage")
                    }
                }
            }
        }
        if ("bottom" in F) {
            if (typeof F.bottom == "number") {
                I = F.bottom
            } else {
                if (typeof F.bottom == "string" && F.bottom.charAt(F.bottom.length - 1) == "%") {
                    I = parseFloat(F.bottom) / 100;
                    H = this.pluginInstance.UNITS_FRACTION
                } else {
                    throw new TypeError("bottom must be a number or string indicating a percentage")
                }
            }
        } else {
            if ("top" in F) {
                if (typeof F.top == "number") {
                    I = F.top;
                    H = this.pluginInstance.UNITS_INSET_PIXELS
                } else {
                    if (typeof F.top == "string" && F.top.charAt(F.top.length - 1) == "%") {
                        I = 1 - parseFloat(F.top) / 100;
                        H = this.pluginInstance.UNITS_FRACTION
                    } else {
                        throw new TypeError("top must be a number or string indicating a percentage")
                    }
                }
            }
        }
        G.set(E, i, I, H)
    };
    v.prototype.dom.computeBounds = function (i) {
        var E = new geo.Bounds();
        this.dom.walk({rootObject: i, features: true, geometries: true, visitCallback: function () {
            if ("getType" in this) {
                var H = this.getType();
                switch (H) {
                    case"KmlGroundOverlay":
                        var G = this.getLatLonBox();
                        if (G) {
                            var J = this.getAltitude();
                            E.extend(new geo.Point(G.getNorth(), G.getEast(), J));
                            E.extend(new geo.Point(G.getNorth(), G.getWest(), J));
                            E.extend(new geo.Point(G.getSouth(), G.getEast(), J));
                            E.extend(new geo.Point(G.getSouth(), G.getWest(), J))
                        }
                        break;
                    case"KmlModel":
                        E.extend(new geo.Point(this.getLocation()));
                        break;
                    case"KmlLinearRing":
                    case"KmlLineString":
                        var I = this.getCoordinates();
                        if (I) {
                            var K = I.getLength();
                            for (var F = 0; F < K; F++) {
                                E.extend(new geo.Point(I.get(F)))
                            }
                        }
                        break;
                    case"KmlCoord":
                    case"KmlLocation":
                    case"KmlPoint":
                        E.extend(new geo.Point(this));
                        break
                }
            }
        }});
        return E
    };
    v.prototype.dom.buildLookAt = g({apiInterface: "KmlLookAt", apiFactoryFn: "createLookAt", defaultProperty: "point", propertySpec: {copy: false, point: d, heading: n, tilt: n, range: n}, constructor: function (F, E) {
        var i = new geo.Point(E.point);
        var H = {heading: 0, tilt: 0, range: 1000};
        if (E.copy) {
            var G = this.util.getLookAt(H.altitudeMode);
            H.heading = G.getHeading();
            H.tilt = G.getTilt();
            H.range = G.getRange()
        }
        E = t(E, true, H);
        F.set(i.lat(), i.lng(), i.altitude(), i.altitudeMode(), E.heading, E.tilt, E.range)
    }});
    v.prototype.dom.buildCamera = g({apiInterface: "KmlCamera", apiFactoryFn: "createCamera", defaultProperty: "point", propertySpec: {copy: false, point: d, heading: n, tilt: n, roll: n}, constructor: function (G, F) {
        var i = new geo.Point(F.point);
        var H = {heading: 0, tilt: 0, roll: 0};
        if (F.copy) {
            var E = this.util.getCamera(H.altitudeMode);
            H.heading = E.getHeading();
            H.tilt = E.getTilt();
            H.roll = E.getRoll()
        }
        F = t(F, true, H);
        G.set(i.lat(), i.lng(), i.altitude(), i.altitudeMode(), F.heading, F.tilt, F.roll)
    }});
    v.prototype.edit = {isnamespace_: true};
    var x = "_GEarthExtensions_dragData";
    var p = null;

    function q(E, i) {
        var F = E.util.getJsDataValue(i, x) || {};
        p = {placemark: i, startAltitude: i.getGeometry().getAltitude(), draggableOptions: F.draggableOptions, dragged: false}
    }

    function f(i) {
        return function (G) {
            if (p) {
                G.preventDefault();
                if (!G.getDidHitGlobe()) {
                    return
                }
                if (!p.dragged) {
                    p.dragged = true;
                    if (p.draggableOptions.draggingStyle) {
                        p.oldStyle = p.placemark.getStyleSelector();
                        p.placemark.setStyleSelector(i.dom.buildStyle(p.draggableOptions.draggingStyle))
                    }
                    if (p.draggableOptions.bounce) {
                        i.fx.cancel(p.placemark);
                        i.fx.bounce(p.placemark, {phase: 1})
                    }
                    if (p.draggableOptions.targetScreenOverlay) {
                        var F = i.dom.buildScreenOverlay(p.draggableOptions.targetScreenOverlay);
                        i.pluginInstance.getFeatures().appendChild(F);
                        p.activeTargetScreenOverlay = F
                    }
                }
                if (p.activeTargetScreenOverlay) {
                    i.dom.setVec2(p.activeTargetScreenOverlay.getOverlayXY(), {left: G.getClientX(), top: G.getClientY()})
                }
                var E = p.placemark.getGeometry();
                E.setLatitude(G.getLatitude());
                E.setLongitude(G.getLongitude());
                p.placemark.setVisibility(true);
                if (p.draggableOptions.dragCallback) {
                    p.draggableOptions.dragCallback.call(p.placemark)
                }
            }
        }
    }

    function D(i, F) {
        if (p) {
            if (p.dragged) {
                if (p.oldStyle) {
                    p.placemark.setStyleSelector(p.oldStyle);
                    delete p.oldStyle
                }
                if (p.activeTargetScreenOverlay) {
                    i.pluginInstance.getFeatures().removeChild(p.activeTargetScreenOverlay);
                    delete p.activeTargetScreenOverlay
                }
                if (p.draggableOptions.bounce) {
                    i.fx.cancel(p.placemark);
                    i.fx.bounce(p.placemark, {startAltitude: p.startAltitude, phase: 2, repeat: 1, dampen: 0.3})
                }
            }
            var E = p;
            p = null;
            if (E.dragged && E.draggableOptions.dropCallback && !F) {
                E.draggableOptions.dropCallback.call(E.placemark)
            }
        }
    }

    v.prototype.edit.makeDraggable = function (G, F) {
        this.edit.endDraggable(G);
        F = t(F, false, {bounce: true, dragCallback: n, dropCallback: n, draggingStyle: n, targetScreenOverlay: n});
        var H = this;
        var E = f(H);
        var I;
        I = function (J) {
            if (p && J.getButton() === 0) {
                google.earth.removeEventListener(H.pluginInstance.getWindow(), "mousemove", E);
                google.earth.removeEventListener(H.pluginInstance.getWindow(), "mouseup", I);
                if (p.dragged) {
                    J.preventDefault()
                }
                D(H)
            }
        };
        var i = function (J) {
            if (J.getButton() === 0) {
                q(H, J.getTarget());
                google.earth.addEventListener(H.pluginInstance.getWindow(), "mousemove", E);
                google.earth.addEventListener(H.pluginInstance.getWindow(), "mouseup", I)
            }
        };
        this.util.setJsDataValue(G, x, {draggableOptions: F, abortAndEndFn: function () {
            if (p && p.placemark.equals(G)) {
                google.earth.removeEventListener(H.pluginInstance.getWindow(), "mousemove", E);
                google.earth.removeEventListener(H.pluginInstance.getWindow(), "mouseup", I);
                D(H, true)
            }
            google.earth.removeEventListener(G, "mousedown", i)
        }});
        google.earth.addEventListener(G, "mousedown", i)
    };
    v.prototype.edit.endDraggable = function (i) {
        var E = this.util.getJsDataValue(i, x);
        if (E) {
            E.abortAndEndFn.call(null);
            this.util.clearJsDataValue(i, x)
        }
    };
    v.prototype.edit.place = function (G, F) {
        F = t(F, false, {bounce: true, dragCallback: n, dropCallback: n, draggingStyle: n, targetScreenOverlay: n});
        var H = this;
        var E = f(H);
        G.setVisibility(false);
        var i;
        i = function (I) {
            if (p && I.getButton() === 0) {
                I.preventDefault();
                I.stopPropagation();
                google.earth.removeEventListener(H.pluginInstance.getWindow(), "mousemove", E);
                google.earth.removeEventListener(H.pluginInstance.getWindow(), "mousedown", i);
                D(H)
            }
        };
        this.util.setJsDataValue(G, x, {draggableOptions: F, abortAndEndFn: function () {
            if (p && p.placemark.equals(G)) {
                google.earth.removeEventListener(H.pluginInstance.getWindow(), "mousemove", E);
                google.earth.removeEventListener(H.pluginInstance.getWindow(), "mousedown", i);
                D(H, true)
            }
        }});
        q(H, G);
        google.earth.addEventListener(H.pluginInstance.getWindow(), "mousemove", E);
        google.earth.addEventListener(H.pluginInstance.getWindow(), "mousedown", i)
    };
    var B = "_GEarthExtensions_lineStringEditData";
    var b = "http://maps.google.com/mapfiles/kml/shapes/placemark_circle.png";
    var l = 0.85;
    var j = 0.6;

    function o(E, i) {
        return E.getLatitude() == i.getLatitude() && E.getLongitude() == i.getLongitude() && E.getAltitude() == i.getAltitude()
    }

    v.prototype.edit.drawLineString = function (L, S) {
        S = t(S, false, {bounce: true, drawCallback: n, finishCallback: n, ensureCounterClockwise: true});
        var J = this.util.getJsDataValue(L, B) || {};
        if (J) {
            this.edit.endEditLineString(L)
        }
        var M = this;
        var F = false;
        var I = new geo.Polygon();
        var K = false;
        var N = [];
        var H = L.getAltitudeMode();
        var Q = null;
        var P = (L.getType() == "KmlLinearRing");
        var O = L.getCoordinates();
        var R = this.pluginInstance.parseKml(["<Document>", '<Style id="_GEarthExtensions_regularCoordinate"><IconStyle>', "<Icon><href>", b, "</href></Icon>", "<scale>", l, "</scale></IconStyle></Style>", '<Style id="_GEarthExtensions_firstCoordinateHighlight"><IconStyle>', "<Icon><href>", b, "</href></Icon>", "<scale>", l * 1.3, "</scale>", "<color>ff00ff00</color></IconStyle></Style>", '<StyleMap id="_GEarthExtensions_firstCoordinate">', "<Pair><key>normal</key>", "<styleUrl>#_GEarthExtensions_regularCoordinate</styleUrl>", "</Pair><Pair><key>highlight</key>", "<styleUrl>#_GEarthExtensions_firstCoordinateHighlight</styleUrl>", "</Pair></StyleMap>", "</Document>"].join(""));
        var i;
        var G = function (W) {
            google.earth.removeEventListener(M.pluginInstance.getWindow(), "dblclick", i);
            var U = O.getLength();
            if (U && P) {
                var V = O.get(0);
                var T = O.get(U - 1);
                if (!o(V, T)) {
                    O.pushLatLngAlt(V.getLatitude(), V.getLongitude(), V.getAltitude())
                }
            }
            M.edit.endDraggable(Q);
            M.dom.removeObject(R);
            M.util.clearJsDataValue(L, B);
            N = [];
            K = true;
            if (S.finishCallback && !W) {
                S.finishCallback.call(null)
            }
        };
        i = function (T) {
            T.preventDefault();
            G.call(null)
        };
        var E;
        E = function () {
            Q = M.dom.buildPointPlacemark([0, 0], {altitudeMode: H, style: "#_GEarthExtensions_regularCoordinate", visibility: false});
            R.getFeatures().appendChild(Q);
            if (F) {
                N.unshift(Q)
            } else {
                N.push(Q)
            }
            M.edit.place(Q, {bounce: S.bounce, dropCallback: function () {
                if (!K) {
                    var T = [Q.getGeometry().getLatitude(), Q.getGeometry().getLongitude(), 0];
                    if (F) {
                        O.unshiftLatLngAlt(T[0], T[1], T[2])
                    } else {
                        O.pushLatLngAlt(T[0], T[1], T[2])
                    }
                    if (S.ensureCounterClockwise) {
                        if (F) {
                            I.outerBoundary().prepend(T)
                        } else {
                            I.outerBoundary().append(T)
                        }
                        if (!I.isCounterClockwise()) {
                            I.outerBoundary().reverse();
                            O.reverse();
                            F = !F
                        }
                    }
                    if (S.drawCallback) {
                        S.drawCallback.call(null, F ? 0 : O.getLength() - 1)
                    }
                    if (N.length == 1) {
                        N[0].setStyleUrl("#_GEarthExtensions_firstCoordinate");
                        google.earth.addEventListener(N[0], "mousedown", function (U) {
                            return function (V) {
                                if (F) {
                                    O.unshiftLatLngAlt(U[0], U[1], U[2])
                                } else {
                                    O.pushLatLngAlt(U[0], U[1], U[2])
                                }
                                i(V)
                            }
                        }(T))
                    }
                    setTimeout(E, 0)
                }
            }})
        };
        E.call(null);
        google.earth.addEventListener(M.pluginInstance.getWindow(), "dblclick", i);
        this.pluginInstance.getFeatures().appendChild(R);
        this.util.setJsDataValue(L, B, {abortAndEndFn: function () {
            G.call(null, true)
        }})
    };
    v.prototype.edit.editLineString = function (K, S) {
        S = t(S, false, {editCallback: n});
        var J = this.util.getJsDataValue(K, B) || {};
        if (J) {
            this.edit.endEditLineString(K)
        }
        var O = this;
        var Q = (K.getType() == "KmlLinearRing");
        var F = K.getAltitudeMode();
        var P = K.getCoordinates();
        var N = P.getLength();
        if (N && Q) {
            var i = P.get(0);
            var L = P.get(N - 1);
            if (!o(i, L)) {
                P.pushLatLngAlt(i.getLatitude(), i.getLongitude(), i.getAltitude());
                N++
            }
        }
        var R = this.pluginInstance.parseKml(["<Document>", '<Style id="_GEarthExtensions_regularCoordinate"><IconStyle>', "<Icon><href>", b, "</href></Icon>", "<color>ffffffff</color>", "<scale>", l, "</scale></IconStyle></Style>", '<StyleMap id="_GEarthExtensions_midCoordinate">', "<Pair><key>normal</key>", "<Style><IconStyle>", "<Icon><href>", b, "</href></Icon>", "<color>60ffffff</color><scale>", j, "</scale></IconStyle></Style></Pair>", "<Pair><key>highlight</key>", "<styleUrl>#_GEarthExtensions_regularCoordinate</styleUrl>", "</Pair></StyleMap>", "</Document>"].join(""));
        var I = [];
        var M = function () {
            if (!Q) {
                return
            }
            if (N == 3) {
                I[1].rightMidPlacemark.setVisibility(false)
            } else {
                if (N >= 4) {
                    I[N - 2].rightMidPlacemark.setVisibility(true)
                }
            }
        };
        var G = function (T) {
            return function (V) {
                V.preventDefault();
                var W = null;
                if (T.index > 0 || Q) {
                    var U = T.index - 1;
                    if (U < 0) {
                        U += N
                    }
                    if (Q && T.index === 0) {
                        U--
                    }
                    W = I[U]
                }
                for (y = T.index; y < N - 1; y++) {
                    P.set(y, P.get(y + 1))
                }
                P.pop();
                if (Q && T.index === 0) {
                    P.set(N - 2, P.get(0))
                }
                N--;
                if (!T.rightMidPlacemark && W) {
                    O.edit.endDraggable(W.rightMidPlacemark);
                    O.dom.removeObject(W.rightMidPlacemark);
                    W.rightMidPlacemark = null
                }
                if (T.rightMidPlacemark) {
                    O.edit.endDraggable(T.rightMidPlacemark);
                    O.dom.removeObject(T.rightMidPlacemark)
                }
                O.edit.endDraggable(T.regularPlacemark);
                google.earth.removeEventListener(T.regularPlacemark, "dblclick", T.deleteEventListener);
                O.dom.removeObject(T.regularPlacemark);
                I.splice(T.index, 1);
                for (y = 0; y < N; y++) {
                    I[y].index = y
                }
                if (W) {
                    W.regularDragCallback.call(W.regularPlacemark, W)
                }
                M();
                if (S.editCallback) {
                    S.editCallback(null)
                }
            }
        };
        var H = function (T) {
            return function () {
                P.setLatLngAlt(T.index, this.getGeometry().getLatitude(), this.getGeometry().getLongitude(), this.getGeometry().getAltitude());
                if (Q && N >= 2 && T.index === 0) {
                    var aa = P.get(0);
                    var Y = P.get(N - 1);
                    P.setLatLngAlt(0, this.getGeometry().getLatitude(), this.getGeometry().getLongitude(), this.getGeometry().getAltitude());
                    P.setLatLngAlt(N - 1, this.getGeometry().getLatitude(), this.getGeometry().getLongitude(), this.getGeometry().getAltitude())
                }
                var U = P.get(T.index);
                if (T.index > 0 || Q) {
                    var W = T.index - 1;
                    if (W < 0) {
                        W += N
                    }
                    if (Q && T.index === 0) {
                        W--
                    }
                    var X = new geo.Point(P.get(W)).midpoint(new geo.Point(U));
                    I[W].rightMidPlacemark.getGeometry().setLatitude(X.lat());
                    I[W].rightMidPlacemark.getGeometry().setLongitude(X.lng());
                    I[W].rightMidPlacemark.getGeometry().setAltitude(X.altitude())
                }
                if (T.index < N - 1 || Q) {
                    var V;
                    if ((Q && T.index == N - 2) || (!Q && T.index == N - 1)) {
                        V = P.get(0)
                    } else {
                        V = P.get(T.index + 1)
                    }
                    var Z = new geo.Point(U).midpoint(new geo.Point(V));
                    T.rightMidPlacemark.getGeometry().setLatitude(Z.lat());
                    T.rightMidPlacemark.getGeometry().setLongitude(Z.lng());
                    T.rightMidPlacemark.getGeometry().setAltitude(Z.altitude())
                }
                M();
                if (S.editCallback) {
                    S.editCallback(null)
                }
            }
        };
        var E = function (T) {
            var U = false;
            var V = null;
            return function () {
                if (!U) {
                    U = true;
                    var W;
                    this.setStyleUrl("#_GEarthExtensions_regularCoordinate");
                    P.push(P.get(N - 1));
                    for (W = N - 1; W > T.index + 1; W--) {
                        P.set(W, P.get(W - 1))
                    }
                    N++;
                    V = {};
                    V.index = T.index + 1;
                    V.regularPlacemark = this;
                    T.rightMidPlacemark = O.dom.buildPointPlacemark({point: P.get(T.index), altitudeMode: F, style: "#_GEarthExtensions_midCoordinate"});
                    R.getFeatures().appendChild(T.rightMidPlacemark);
                    O.edit.makeDraggable(T.rightMidPlacemark, {bounce: false, dragCallback: E(T)});
                    V.rightMidPlacemark = O.dom.buildPointPlacemark({point: P.get(T.index), altitudeMode: F, style: "#_GEarthExtensions_midCoordinate"});
                    R.getFeatures().appendChild(V.rightMidPlacemark);
                    O.edit.makeDraggable(V.rightMidPlacemark, {bounce: false, dragCallback: E(V)});
                    V.deleteEventListener = G(V);
                    google.earth.addEventListener(this, "dblclick", V.deleteEventListener);
                    V.regularDragCallback = H(V);
                    I.splice(V.index, 0, V);
                    for (W = 0; W < N; W++) {
                        I[W].index = W
                    }
                }
                V.regularDragCallback.call(this, V)
            }
        };
        O.util.batchExecute(function () {
            for (var V = 0; V < N; V++) {
                var T = P.get(V);
                var W = P.get((V + 1) % N);
                var U = {};
                I.push(U);
                U.index = V;
                if (Q && V == N - 1) {
                    continue
                }
                U.regularPlacemark = O.dom.buildPointPlacemark(T, {altitudeMode: F, style: "#_GEarthExtensions_regularCoordinate"});
                R.getFeatures().appendChild(U.regularPlacemark);
                U.regularDragCallback = H(U);
                O.edit.makeDraggable(U.regularPlacemark, {bounce: false, dragCallback: U.regularDragCallback});
                U.deleteEventListener = G(U);
                google.earth.addEventListener(U.regularPlacemark, "dblclick", U.deleteEventListener);
                if (V < N - 1 || Q) {
                    U.rightMidPlacemark = O.dom.buildPointPlacemark({point: new geo.Point(T).midpoint(new geo.Point(W)), altitudeMode: F, style: "#_GEarthExtensions_midCoordinate"});
                    R.getFeatures().appendChild(U.rightMidPlacemark);
                    O.edit.makeDraggable(U.rightMidPlacemark, {bounce: false, dragCallback: E(U)})
                }
            }
            M();
            O.pluginInstance.getFeatures().appendChild(R)
        });
        O.util.setJsDataValue(K, B, {innerDoc: R, abortAndEndFn: function () {
            O.util.batchExecute(function () {
                var V = P.getLength();
                if (V && Q) {
                    var W = P.get(0);
                    var U = P.get(V - 1);
                    if (!o(W, U)) {
                        P.pushLatLngAlt(W.getLatitude(), W.getLongitude(), W.getAltitude())
                    }
                }
                for (var T = 0; T < I.length; T++) {
                    if (!I[T].regularPlacemark) {
                        continue
                    }
                    google.earth.removeEventListener(I[T].regularPlacemark, "dblclick", I[T].deleteEventListener);
                    O.edit.endDraggable(I[T].regularPlacemark);
                    if (I[T].rightMidPlacemark) {
                        O.edit.endDraggable(I[T].rightMidPlacemark)
                    }
                }
                O.dom.removeObject(R)
            })
        }})
    };
    v.prototype.edit.endEditLineString = function (E) {
        var i = this.util.getJsDataValue(E, B);
        if (i) {
            i.abortAndEndFn.call(null);
            this.util.clearJsDataValue(E, B)
        }
    };
    v.prototype.fx = {isnamespace_: true};
    v.prototype.fx.AnimationManager_ = s(function () {
        this.extInstance = arguments.callee.extInstance_;
        this.animations_ = [];
        this.running_ = false;
        this.globalTime_ = 0
    });
    v.prototype.fx.AnimationManager_.prototype.startAnimation = function (i) {
        this.animations_.push({obj: i, startGlobalTime: this.globalTime_});
        this.start_()
    };
    v.prototype.fx.AnimationManager_.prototype.stopAnimation = function (F) {
        for (var E = 0; E < this.animations_.length; E++) {
            if (this.animations_[E].obj == F) {
                this.animations_.splice(E, 1);
                return
            }
        }
    };
    v.prototype.fx.AnimationManager_.prototype.start_ = function () {
        if (this.running_) {
            return
        }
        this.startTimeStamp_ = Number(new Date());
        this.tick_();
        for (var E = 0; E < this.animations_.length; E++) {
            this.animations_[E].obj.renderFrame(0)
        }
        var F = this;
        this.frameendListener_ = function () {
            F.tick_()
        };
        this.tickInterval_ = window.setInterval(this.frameendListener_, 100);
        google.earth.addEventListener(this.extInstance.pluginInstance, "frameend", this.frameendListener_);
        this.running_ = true
    };
    v.prototype.fx.AnimationManager_.prototype.stop_ = function () {
        if (!this.running_) {
            return
        }
        google.earth.removeEventListener(this.extInstance.pluginInstance, "frameend", this.frameendListener_);
        this.frameendListener_ = null;
        window.clearInterval(this.tickInterval_);
        this.tickInterval_ = null;
        this.running_ = false;
        this.globalTime_ = 0
    };
    v.prototype.fx.AnimationManager_.prototype.tick_ = function () {
        if (!this.running_) {
            return
        }
        this.globalTime_ = Number(new Date()) - this.startTimeStamp_;
        this.renderCurrentFrame_()
    };
    v.prototype.fx.AnimationManager_.prototype.renderCurrentFrame_ = function () {
        for (var E = this.animations_.length - 1; E >= 0; E--) {
            var F = this.animations_[E];
            F.obj.renderFrame(this.globalTime_ - F.startGlobalTime)
        }
        if (this.animations_.length === 0) {
            this.stop_()
        }
    };
    v.prototype.fx.getAnimationManager_ = function () {
        if (!this.fx.animationManager_) {
            this.fx.animationManager_ = new this.fx.AnimationManager_()
        }
        return this.fx.animationManager_
    };
    v.prototype.fx.Animation = s(function (E, i) {
        this.extInstance = arguments.callee.extInstance_;
        this.renderFn = E;
        this.completionFn = i || function () {
        }
    });
    v.prototype.fx.Animation.prototype.start = function () {
        this.extInstance.fx.getAnimationManager_().startAnimation(this)
    };
    v.prototype.fx.Animation.prototype.stop = function (i) {
        this.extInstance.fx.getAnimationManager_().stopAnimation(this);
        this.completionFn({cancelled: !Boolean(i || geo.util.isUndefined(i))})
    };
    v.prototype.fx.Animation.prototype.rewind = function () {
        this.renderFrame(0);
        this.stop(false)
    };
    v.prototype.fx.Animation.prototype.renderFrame = function (i) {
        this.renderFn.call(this, i)
    };
    v.prototype.fx.TimedAnimation = s([v.prototype.fx.Animation], function (F, E, i) {
        this.extInstance = arguments.callee.extInstance_;
        this.duration = F;
        this.renderFn = E;
        this.complete = false;
        this.completionFn = i || function () {
        }
    });
    v.prototype.fx.TimedAnimation.prototype.renderFrame = function (i) {
        if (this.complete) {
            return
        }
        if (i > this.duration) {
            this.renderFn.call(this, this.duration);
            this.stop();
            this.complete = true;
            return
        }
        this.renderFn.call(this, i)
    };
    v.prototype.fx.bounce = function (I, H) {
        H = t(H, false, {duration: 300, startAltitude: n, altitude: this.util.getCamera().getAltitude() / 5, phase: n, repeat: 0, dampen: 0.3, callback: function () {
        }});
        var J = this;
        this.fx.rewind(I);
        if (!"getGeometry" in I || !I.getGeometry() || I.getGeometry().getType() != "KmlPoint") {
            throw new TypeError("Placemark must be a KmlPoint geometry")
        }
        var i = I.getGeometry();
        var G = i.getAltitudeMode();
        if (G == this.pluginInstance.ALTITUDE_CLAMP_TO_GROUND) {
            i.setAltitude(0);
            i.setAltitudeMode(this.pluginInstance.ALTITUDE_RELATIVE_TO_GROUND)
        }
        if (G == this.pluginInstance.ALTITUDE_CLAMP_TO_SEA_FLOOR) {
            i.setAltitude(0);
            i.setAltitudeMode(this.pluginInstance.ALTITUDE_RELATIVE_TO_SEA_FLOOR)
        }
        if (typeof H.startAltitude != "number") {
            H.startAltitude = i.getAltitude()
        }
        var F, E;
        F = function () {
            J.fx.animateProperty(i, "altitude", {duration: H.duration / 2, end: H.startAltitude + H.altitude, easing: "out", featureProxy: I, callback: E || function () {
            }})
        };
        E = function (K) {
            if (K && K.cancelled) {
                return
            }
            J.fx.animateProperty(i, "altitude", {duration: H.duration / 2, start: H.startAltitude + H.altitude, end: H.startAltitude, easing: "in", featureProxy: I, callback: function (L) {
                i.setAltitudeMode(G);
                if (L.cancelled) {
                    i.setAltitude(H.startAltitude);
                    H.callback.call(I, L);
                    return
                }
                if (H.repeat >= 1) {
                    --H.repeat;
                    H.altitude *= H.dampen;
                    H.duration *= Math.sqrt(H.dampen);
                    H.phase = 0;
                    J.fx.bounce(I, H)
                } else {
                    H.callback.call(I, L)
                }
            }})
        };
        if (H.phase === 1) {
            E = null;
            F.call()
        } else {
            if (H.phase === 2) {
                E.call()
            } else {
                F.call()
            }
        }
    };
    v.prototype.fx.cancel = function (F) {
        var G = this.util.getJsDataValue(F, "_GEarthExtensions_anim") || [];
        for (var E = 0; E < G.length; E++) {
            G[E].stop(false)
        }
    };
    v.prototype.fx.rewind = function (F) {
        var G = this.util.getJsDataValue(F, "_GEarthExtensions_anim") || [];
        for (var E = 0; E < G.length; E++) {
            G[E].rewind()
        }
    };
    v.prototype.fx.animateProperty = function (F, L, N) {
        N = t(N, false, {duration: 500, start: n, end: n, delta: n, easing: "none", callback: n, featureProxy: n});
        if (typeof N.easing == "string") {
            N.easing = {none: function (O) {
                return O
            }, "in": function (O) {
                return O * O * O
            }, out: function (P) {
                var Q = P * P;
                var O = Q * P;
                return O - 3 * Q + 3 * P
            }, both: function (P) {
                var Q = P * P;
                var O = Q * P;
                return 6 * O * Q - 15 * Q * Q + 10 * O
            }}[N.easing]
        }
        var G = L.charAt(0).toUpperCase() + L.substr(1);
        var J = this;
        var H;
        if (L == "color") {
            if (N.delta) {
                throw new Error("Cannot use delta with color animations.")
            }
            var I = F.getColor() || {get: function () {
                return""
            }};
            if (!N.start) {
                N.start = I.get()
            }
            if (!N.end) {
                N.end = I.get()
            }
            H = function (O) {
                I.set(J.util.blendColors(N.start, N.end, N.easing.call(null, O)))
            }
        } else {
            var K = function () {
                return J.util.callMethod(F, "get" + G)
            };
            var E = function (O) {
                return J.util.callMethod(F, "set" + G, O)
            };
            if (!isFinite(N.start) && !isFinite(N.end)) {
                if (!isFinite(N.delta)) {
                    N.delta = 0
                }
                N.start = K();
                N.end = K() + N.delta
            } else {
                if (!isFinite(N.start)) {
                    N.start = K()
                }
                if (!isFinite(N.end)) {
                    N.end = K()
                }
            }
            H = function (O) {
                E(N.start + (N.end - N.start) * N.easing.call(null, O))
            }
        }
        var i = new this.fx.TimedAnimation(N.duration, function (O) {
            H(1 * O / N.duration)
        }, function (P) {
            var Q = J.util.getJsDataValue(N.featureProxy || F, "_GEarthExtensions_anim");
            if (Q) {
                for (var O = 0; O < Q.length; O++) {
                    if (Q[O] == this) {
                        Q.splice(O, 1);
                        break
                    }
                }
                if (!Q.length) {
                    J.util.clearJsDataValue(N.featureProxy || F, "_GEarthExtensions_anim")
                }
            }
            if (N.callback) {
                N.callback.call(F, P)
            }
        });
        var M = this.util.getJsDataValue(N.featureProxy || F, "_GEarthExtensions_anim");
        if (M) {
            M.push(i)
        } else {
            this.util.setJsDataValue(N.featureProxy || F, "_GEarthExtensions_anim", [i])
        }
        i.start();
        return i
    };
    v.prototype.math3d = {isnamespace_: true};
    function u(S) {
        var U = 2;
        var T = 0;
        var R = 1;
        var L = [
            [0, 0, 0],
            [0, 0, 0],
            [0, 0, 0]
        ];
        var P = Math.cos(S[0]);
        var N = Math.cos(S[1]);
        var Q = Math.cos(S[2]);
        var G = Math.sin(S[0]);
        var E = Math.sin(S[1]);
        var H = Math.sin(S[2]);
        var i = P * Q;
        var M = P * H;
        var F = G * Q;
        var O = G * H;
        L[U][U] = N * Q;
        L[U][T] = E * F - M;
        L[U][R] = E * i + O;
        L[T][U] = N * H;
        L[T][T] = E * O + i;
        L[T][R] = E * M - F;
        L[R][U] = -E;
        L[R][T] = N * G;
        L[R][R] = N * P;
        return new geo.linalg.Matrix(L)
    }

    function k(F) {
        var G = 2 + 1;
        var E = 0 + 1;
        var i = 1 + 1;
        var H = 0.000001;
        var L = Math.sqrt(F.e(G, G) * F.e(G, G) + F.e(E, G) * F.e(E, G));
        if (L <= 16 * H) {
            return[Math.atan2(-F.e(E, i), F.e(E, E)), Math.atan2(-F.e(i, G), L), 0]
        }
        return[Math.atan2(F.e(i, E), F.e(i, i)), Math.atan2(-F.e(i, G), L), Math.atan2(F.e(E, G), F.e(G, G))]
    }

    v.prototype.math3d.htrToLocalFrame = function (i) {
        return u([i[0].toRadians(), i[1].toRadians(), i[2].toRadians()])
    };
    v.prototype.math3d.localFrameToHtr = function (i) {
        var E = k(i);
        return[E[0].toDegrees(), E[1].toDegrees(), E[2].toDegrees()]
    };
    v.prototype.math3d.makeOrthonormalFrame = function (E, i) {
        var G = E.cross(i).toUnitVector();
        if (G.eql(geo.linalg.Vector.Zero(3))) {
            return null
        }
        var H = i.cross(G).toUnitVector();
        var F = G.cross(H);
        return new geo.linalg.Matrix([G.elements, H.elements, F.elements])
    };
    v.prototype.math3d.makeLocalToGlobalFrame = function (i) {
        var E = i.toCartesian().toUnitVector();
        var F = new geo.linalg.Vector([0, 1, 0]).cross(E).toUnitVector();
        var G = E.cross(F).toUnitVector();
        return new geo.linalg.Matrix([F.elements, G.elements, E.elements])
    };
    v.prototype.util = {isnamespace_: true};
    v.NAMED_COLORS = {aqua: "ffffff00", black: "ff000000", blue: "ffff0000", fuchsia: "ffff00ff", gray: "ff808080", green: "ff008000", lime: "ff00ff00", maroon: "ff000080", navy: "ff800000", olive: "ff008080", purple: "ff800080", red: "ff0000ff", silver: "ffc0c0c0", teal: "ff808000", white: "ffffffff", yellow: "ff00ffff"};
    v.prototype.util.parseColor = function (i, E) {
        var G = function (H) {
            return((H.length < 2) ? "0" : "") + H
        };
        if (geo.util.isArray(i)) {
            return G(((i.length >= 4) ? i[3].toString(16) : "ff")) + G(i[2].toString(16)) + G(i[1].toString(16)) + G(i[0].toString(16))
        } else {
            if (typeof i == "string") {
                if (i.toLowerCase() in v.NAMED_COLORS) {
                    return v.NAMED_COLORS[i.toLowerCase()]
                }
                if (i.length > 7) {
                    return i.match(/^[0-9a-f]{8}$/i) ? i : null
                } else {
                    var F = null;
                    if (i.length > 4) {
                        F = i.replace(/#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/i, "ff$3$2$1").toLowerCase()
                    } else {
                        F = i.replace(/#?([0-9a-f])([0-9a-f])([0-9a-f])/i, "ff$3$3$2$2$1$1").toLowerCase()
                    }
                    if (F == i) {
                        return null
                    }
                    if (!geo.util.isUndefined(E)) {
                        F = G(Math.floor(255 * E).toString(16)) + F.substring(2)
                    }
                    return F
                }
            }
        }
        return null
    };
    v.prototype.util.blendColors = function (E, i, F) {
        if (geo.util.isUndefined(F) || F === null) {
            F = 0.5
        }
        E = this.util.parseColor(E);
        i = this.util.parseColor(i);
        var G = function (I) {
            return((I.length < 2) ? "0" : "") + I
        };
        var H = function (J, I) {
            J = parseInt(J, 16);
            I = parseInt(I, 16);
            return G(Math.floor((I - J) * F + J).toString(16))
        };
        return H(E.substr(0, 2), i.substr(0, 2)) + H(E.substr(2, 2), i.substr(2, 2)) + H(E.substr(4, 2), i.substr(4, 2)) + H(E.substr(6, 2), i.substr(6, 2))
    };
    var A = {};

    function m() {
        var F = [], G = "0123456789ABCDEF", E = 0;
        for (E = 0; E < 36; E++) {
            F[E] = Math.floor(Math.random() * 16)
        }
        F[14] = 4;
        F[19] = (F[19] & 3) | 8;
        for (E = 0; E < 36; E++) {
            F[E] = G.charAt(F[E])
        }
        F[8] = F[13] = F[18] = F[23] = "-";
        return F.join("")
    }

    function e(E) {
        for (var i in A) {
            if (A[i].object.equals(E)) {
                return i
            }
        }
        return null
    }

    v.prototype.util.hasJsData = function (i) {
        return e(i) ? true : false
    };
    v.prototype.util.clearAllJsData = function (E) {
        var i = e(E);
        if (i) {
            delete A[i]
        }
    };
    v.prototype.util.getJsDataValue = function (E, F) {
        var i = e(E);
        if (i && F in A[i].data) {
            return A[i].data[F]
        }
        return undefined
    };
    v.prototype.util.setJsDataValue = function (E, F, G) {
        var i = e(E);
        if (!i) {
            i = null;
            while (!i || i in A) {
                i = m()
            }
            A[i] = {object: E, data: {}}
        }
        A[i].data[F] = G
    };
    v.prototype.util.clearJsDataValue = function (F, G) {
        var i = e(F);
        if (i && G in A[i].data) {
            delete A[i].data[G];
            for (var E in A[i].data) {
                return
            }
            this.util.clearAllJsData(F)
        }
    };
    v.prototype.util.displayKml = function (E, i) {
        i = t(i, false, {cacheBuster: false, flyToView: false, flyToBoundsFallback: true, aspectRatio: 1});
        if (i.cacheBuster) {
            E += (E.match(/\?/) ? "&" : "?") + "_cacheBuster=" + Number(new Date()).toString()
        }
        var F = this;
        google.earth.fetchKml(F.pluginInstance, E, function (G) {
            if (G) {
                F.pluginInstance.getFeatures().appendChild(G);
                if (i.flyToView) {
                    F.util.flyToObject(G, {boundsFallback: i.flyToBoundsFallback, aspectRatio: i.aspectRatio})
                }
            }
        })
    };
    v.prototype.util.displayKmlString = function (F, i) {
        i = t(i, false, {flyToView: false, flyToBoundsFallback: true, aspectRatio: 1});
        var E = this.pluginInstance.parseKml(F);
        if (E) {
            this.pluginInstance.getFeatures().appendChild(E);
            if (i.flyToView) {
                this.util.flyToObject(E, {boundsFallback: i.flyToBoundsFallback, aspectRatio: i.aspectRatio})
            }
        }
        return E
    };
    v.prototype.util.lookAt = function () {
        this.pluginInstance.getView().setAbstractView(this.dom.buildLookAt.apply(null, arguments))
    };
    v.prototype.util.getLookAt = function (i) {
        if (geo.util.isUndefined(i)) {
            i = this.pluginInstance.ALTITUDE_ABSOLUTE
        }
        return this.pluginInstance.getView().copyAsLookAt(i)
    };
    v.prototype.util.getCamera = function (i) {
        if (geo.util.isUndefined(i)) {
            i = this.pluginInstance.ALTITUDE_ABSOLUTE
        }
        return this.pluginInstance.getView().copyAsCamera(i)
    };
    v.prototype.util.flyToObject = function (F, i) {
        i = t(i, false, {boundsFallback: true, aspectRatio: 1});
        if (!F) {
            throw new Error("flyToObject was given an invalid object.")
        }
        if ("getAbstractView" in F && F.getAbstractView()) {
            this.pluginInstance.getView().setAbstractView(F.getAbstractView())
        } else {
            if (i.boundsFallback) {
                var E = this.dom.computeBounds(F);
                if (E && !E.isEmpty()) {
                    this.view.setToBoundsView(E, {aspectRatio: i.aspectRatio})
                }
            }
        }
    };
    v.prototype.util.batchExecute = function (F, i) {
        var E = this;
        google.earth.executeBatch(this.pluginInstance, function () {
            F.call(E, i)
        })
    };
    v.prototype.util.callMethod = function (object, method) {
        var F;
        var args = [];
        for (F = 2; F < arguments.length; F++) {
            args.push(arguments[F])
        }
        if (typeof object[method] == "function") {
            return object[method].apply(object, args)
        } else {
            var E = [];
            for (F = 0; F < args.length; F++) {
                E.push("args[" + F + "]")
            }
            return window["eval"]("object." + method + "(" + E.join(",") + ")")
        }
    };
    v.prototype.util.takeOverCamera = function (i) {
        if (i || geo.util.isUndefined(i)) {
            if (this.cameraControlOldProps_) {
                return
            }
            this.cameraControlOldProps_ = {flyToSpeed: this.pluginInstance.getOptions().getFlyToSpeed(), mouseNavEnabled: this.pluginInstance.getOptions().getMouseNavigationEnabled(), navControlVis: this.pluginInstance.getNavigationControl().getVisibility()};
            this.pluginInstance.getOptions().setFlyToSpeed(this.pluginInstance.SPEED_TELEPORT);
            this.pluginInstance.getOptions().setMouseNavigationEnabled(false);
            this.pluginInstance.getNavigationControl().setVisibility(this.pluginInstance.VISIBILITY_HIDE)
        } else {
            if (!this.cameraControlOldProps_) {
                return
            }
            this.pluginInstance.getOptions().setFlyToSpeed(this.cameraControlOldProps_.flyToSpeed);
            this.pluginInstance.getOptions().setMouseNavigationEnabled(this.cameraControlOldProps_.mouseNavEnabled);
            this.pluginInstance.getNavigationControl().setVisibility(this.cameraControlOldProps_.navControlVis);
            delete this.cameraControlOldProps_
        }
    };
    var C = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_";
    v.prototype.util.encodeArray = function (E) {
        var G = "";
        for (var F = 0; F < E.length; F++) {
            var H = E[F] << 1;
            H = (E[F] < 0) ? ~H : H;
            while (H >= 32) {
                G += C.charAt(32 | (H & 31));
                H >>= 5
            }
            G += C.charAt(H)
        }
        return G
    };
    v.prototype.util.decodeArray = function (I) {
        var F = I.length;
        var H = 0;
        var J = [];
        while (H < F) {
            var E;
            var G = 0;
            var i = 0;
            do {
                E = C.indexOf(I.charAt(H++));
                i |= (E & 31) << G;
                G += 5
            } while (E >= 32);
            J.push(((i & 1) ? ~(i >> 1) : (i >> 1)))
        }
        return J
    };
    v.prototype.view = {isnamespace_: true};
    v.prototype.view.createBoundsView = function (i, N) {
        N = t(N, false, {aspectRatio: d, defaultRange: 1000, scaleRange: 1.5});
        var F = i.center();
        var G = N.defaultRange;
        var J = i.span();
        if (J.lat || J.lng) {
            var M = new geo.Point(F.lat(), i.east()).distance(new geo.Point(F.lat(), i.west()));
            var E = new geo.Point(i.north(), F.lng()).distance(new geo.Point(i.south(), F.lng()));
            var L = Math.min(Math.max(N.aspectRatio, M / E), 1);
            var H = (45 / (L + 0.4) - 2).toRadians();
            var K = Math.max(E, M);
            var I = Math.min((90).toRadians(), H + K / (2 * geo.math.EARTH_RADIUS));
            G = N.scaleRange * geo.math.EARTH_RADIUS * (Math.sin(I) * Math.sqrt(1 + 1 / Math.pow(Math.tan(H), 2)) - 1)
        }
        return this.dom.buildLookAt(new geo.Point(F.lat(), F.lng(), i.top(), i.northEastTop().altitudeMode()), {range: G})
    };
    v.prototype.view.setToBoundsView = function () {
        this.pluginInstance.getView().setAbstractView(this.view.createBoundsView.apply(this, arguments))
    };
    var r = 1073741824;

    function w(i, E) {
        var F = Math.floor(E.altitude * 10);
        return i.util.encodeArray([Math.floor(geo.math.constrainValue(E.lat, [-90, 90]) * 100000), Math.floor(geo.math.wrapValue(E.lng, [-180, 180]) * 100000), Math.floor(F / r), (F >= 0) ? F % r : (r - Math.abs(F) % r), Math.floor(geo.math.wrapValue(E.heading, [0, 360]) * 10), Math.floor(geo.math.wrapValue(E.tilt, [0, 180]) * 10), Math.floor(geo.math.wrapValue(E.roll, [-180, 180]) * 10)])
    }

    function a(E, F) {
        var i = E.util.decodeArray(F);
        return{lat: geo.math.constrainValue(i[0] * 0.00001, [-90, 90]), lng: geo.math.wrapValue(i[1] * 0.00001, [-180, 180]), altitude: (r * i[2] + i[3]) * 0.1, heading: geo.math.wrapValue(i[4] * 0.1, [0, 360]), tilt: geo.math.wrapValue(i[5] * 0.1, [0, 180]), roll: geo.math.wrapValue(i[6] * 0.1, [-180, 180])}
    }

    v.prototype.view.serialize = function () {
        var i = this.pluginInstance.getView().copyAsCamera(this.pluginInstance.ALTITUDE_ABSOLUTE);
        return"0" + w(this, {lat: i.getLatitude(), lng: i.getLongitude(), altitude: i.getAltitude(), heading: i.getHeading(), tilt: i.getTilt(), roll: i.getRoll()})
    };
    v.prototype.view.deserialize = function (F) {
        if (F.charAt(0) != "0") {
            throw new Error("Invalid serialized view string.")
        }
        var E = a(this, F.substr(1));
        var i = this.pluginInstance.createCamera("");
        i.set(E.lat, E.lng, E.altitude, this.pluginInstance.ALTITUDE_ABSOLUTE, E.heading, E.tilt, E.roll);
        this.pluginInstance.getView().setAbstractView(i)
    };
    v.prototype.util.serializeView = v.prototype.view.serialize;
    v.prototype.util.deserializeView = v.prototype.view.deserialize;
    v.prototype.view.createVantageView = function (i, K) {
        i = new geo.Point(i);
        K = new geo.Point(K);
        var M = i.heading(K);
        var E = 0;
        var I = i.toCartesian();
        var J = K.toCartesian();
        var F = this.math3d.makeLocalToGlobalFrame(i);
        var H = J.subtract(I).toUnitVector();
        var G = new geo.linalg.Vector(F.elements[2]).multiply(-1);
        var L = Math.acos(G.dot(H)).toDegrees();
        return this.dom.buildCamera(i, {heading: M, tilt: L})
    };
    window.GEarthExtensions = v
})();