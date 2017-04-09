;(function(w, d, $){
	var root_path = getZsrc()
	var libs_path = root_path.replace('js/', '')+'libs/'
	var isMobile = isMobile()
	var zIndex = 99

	var aniBoxTops = []

	var z = {
		version: '1.0.0',
		root_path: root_path,
		libs_path: libs_path,
		zIndex: function(){
			return ++zIndex
		},
		anims: ['z-anim-upbit','z-anim-scale','z-anim-scaleSpring','z-anim-up'],
		libs: {
			form: {
				src: libs_path + 'jquery.form.min'
			},
			unslider: {
				src: libs_path + 'unslider.min'
			},
			masonry: {
				src: libs_path + 'masonry-docs.min'
			},
			jscrollpane: {
				src: libs_path + 'jquery.jscrollpane.min'
			},
			datetimepicker: {
				src: libs_path + 'bootstrap-datetimepicker.min'
			},
			lazyload: {
				src: libs_path + 'jqquery-lazyload.min'
			}
		},
		title: {
			alert: '警告框',
			confirm: '询问框',
			prompt: '输入框',
			open: '提示窗体'
		},
		isMobile: isMobile
	}

	$.extend({
		createShade: function(next, cb, opacity){
			if(!$('.z-shade').length){
				var html = $('<div class="z-shade" style="z-index:'+z.zIndex()+';'+function(){return opacity?'opacity:'+opacity:''}()+'"></div>')
				$('body').addClass('overflow').append(html)
				html.fadeIn(300, next)
			}else{
				next()
			}
			html.on('click', function(){
				if(cb)	cb()
			})
		},
		resetForm: function(box){
			var boxs = []
			if($.type(box) === 'undefined'){
				var radios = $('input[type="radio"]')
				if(radios.length)	boxs.push(radios)
				var checkboxs = $('input[type="checkbox"]')
				if(checkboxs.length)	boxs.push(checkboxs)
			}else{
				if($.type(box) !== 'object' || !box.length)	box = $(box)
				boxs.push(box)
			}
			if(boxs.length){
				for(var i=0;i<boxs.length;i++){
					if(boxs[i].length)	resetForm(boxs[i])
				}
			}
		},
		submit: function(ele, cb){
			var form = $.type(ele) === 'object' ? ele : $(ele)
			if(!form || !form.length || !form.attr('action'))	return false
			if($.fn.ajaxSubmit && z.libs.form.isloaded){
				doing()
			}else{
				$.loadJs(z.libs.form.src, function(){
					z.libs.form.isloaded = true
					doing()
				})
			}
			function doing(){
				form.ajaxForm();
				form.ajaxSubmit({
					success: function(responseText, statusText, xhr, $form) {
						if(statusText == "success") {
							var ret = responseText
							if($.type(ret) === 'string'){
								if(w.JSON){
									ret = JSON.parse(responseText)
								}else{
									ret = eval(responseText)
								}
							}
							cb(ret)
						}
					}
				});
			}
		},
		alert: function(content, title){
			showMsg('alert', content || '', title || z.title.alert, ['', '确认'], function(obj, i){
				doClose(obj)
			})
		},
		open: function(content, title){
			showMsg('open', content || '', title || z.title.open, ['', '确认'], function(obj, i){
				doClose(obj)
			})
		},
		confirm: function(content, title, btns, cb){
			pro_firm('confirm', content, title, btns, cb)
		},
		prompt: function(title, btns, cb){
			pro_firm('prompt', '', title, btns, cb)
		},
		toast: function(content, color){
			showMsg('toast', content || '', color || 'red')
		},
		success: function(content){
			showMsg('success', content || '', 'green')
		},
		msg: function(content){
			showMsg('msg', content || '')
		},
		loadJs: function(files, cb){
			include('js', files, cb)
		},
		loadCss: function(files, cb){
			include('css', files, cb)
		},
		back: function(){
			w.history.go(-1)
		},
		reload: function(){
			location.reload()
		},
		supportCss3: supportCss3
	})

	$.fn.extend({
		tagName: function(){
			return $(this)[0].tagName.toLowerCase()
		},
		zdata: function(name,val){
			if($.type(val) === 'undefined'){
				return $(this).attr('zdata-'+name)
			}else{
				$(this).attr('zdata-'+name, val)
			}
		},
		button: function(type){
			if(!$(this).length)	return
			var t = null
			if($(this).hasClass('z-disabled')){
				t = 'reset'
			}else{
				t = 'loading'
			}
			type = type || t
			button($(this), type)
		},
		modal: function(type){
			if(!$(this).length)	return
			var _this = $(this)
			if(!_this.hasClass('z-modal'))	return
			if('show' === type && _this.is(':visited'))	return
			if('hide' === type && _this.is(':hidden'))	return
			if(_this.is(':hidden')){
				$.createShade(function(){
					_this.css('zIndex',z.zIndex()).show().addClass('z-anim-downbit z-anim-ms3')
				})
			}else{
				doClose(_this, null, false)
			}
		},
		tips: function(opts){
			if(!$(this).length)	return
			var inits = {
				bgColor: 'black',
				txtColor: 'white',
				autoDie: true
			}
			var options = $.extend({}, inits, opts)
			tips($(this).selector, options)
		},
		move: function(opts){
			var inits = {
				box: '.z-move',
				top: 0,
				right: 0,
				bottom: 0,
				left: 0
			}
			var options = getOpts($(this), inits, opts)
			move($(this).selector, options)
		},
		slider: function(opts){
			if(!$(this).length)	return
			var inits = {
				speed: 500,              
				delay: 5000,             
				complete: null, 
				keys: true,              
				dots: true,              
				prev: '.z-slider-prev',
				next: '.z-slider-next',
				items: 'ul',
				item: 'li'
			}
			var options = $.extend({}, inits, opts)
			$(this).each(function(){
				slider($(this), options)
			})
		},
		waterfall: function(opts){
			if(!$(this).length)	return
			var container = $(this);
			if($.fn.masonry && z.libs.masonry.isloaded){
				doing()
			}else{
				$.loadJs(z.libs.masonry.src, function(){
					z.libs.masonry.isloaded = true
					doing()
				})
			}
			function doing(){
				var inits = {
					itemSelector: '.z-waterfall-item',
	                gutter: 0,
	                isAnimated: true
				}
				var options = getOpts(container, inits, opts)
		        container.css('opacity', 0).imagesLoaded(function() {
		            container.masonry(opts);
	                container.css('opacity', 1)
		        });
			}
		},
		datepicker: function(opts){
			if(!$(this).length)	return
			var container = $(this);
			if($.fn.datetimepicker && z.libs.datetimepicker.isloaded){
				doing()
			}else{
				$.loadCss(z.libs.datetimepicker.src, function(){
					$.loadJs(z.libs.datetimepicker.src, function(){
						z.libs.masonry.isloaded = true
						doing()
					})
				})
			}
			function doing(){
				var inits = {
					minView: "month",
			        format : "yyyy-mm-dd",
			        autoclose: true
				}
				var options = getOpts(container, inits, opts)
		        container.datetimepicker(options)
			}
		},
		goTop: function(opts){
			if(!$(this).length)	return
			var inits = {
				top: 100,
				time: 300
			}
			var rollBtn = $(this)
			var options = getOpts(rollBtn, inits, opts)
			$(w).on('scroll',function(){
		    	if(!rollBtn.length)	return
				if($(this).scrollTop() > options.top){
					rollBtn.addClass('on')
				}else{
					rollBtn.removeClass('on')
				}
			})
			rollBtn.on('click', function(e){
				var ev = e || event
				ev.preventDefault()
				$('body,html').animate({
					scrollTop: 0 ,
					}, options.time
				)
			})
		},
		code: function(opts){
			if(!$(this).length)	return
			$(this).each(function(){
				var options = opts || {}
			    var othis = $(this), html = othis.html();		      
			    if(othis.zdata('title'))	options.title = othis.zdata('title')
			    if(othis.zdata('skin'))	options.skin = othis.zdata('skin')
			    if(othis.zdata('encode'))	options.encode = othis.zdata('encode')
			    if(othis.zdata('about-text') && othis.zdata('about-link'))	options.about = '<a href="'+othis.zdata('about-link')+' target="_blank>'+othis.zdata('about-text')+'</a>'

			    if(options.encode){
					html = html.replace(/&(?!#?[a-zA-Z0-9]+;)/g, '&amp;')
					.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/'/g, '&#39;').replace(/"/g, '&quot;')
				}

				othis.html('<ol class="z-code-ol"><li>' + html.replace(/[\r\t\n]+/g, '</li><li>') + '</li></ol>')
				if(!othis.find('>.z-code-h3')[0] && (options.title || options.about)){
					othis.prepend('<h3 class="z-code-h3">'+ (options.title || 'code') + (options.about || '') + '</h3>');
				}

				var ol = othis.find('>.z-code-ol');
				othis.addClass('z-code-view');
				if(options.skin)	othis.addClass('z-code-'+options.skin)

				//按行数适配左边距
				if((ol.find('li').length/100|0) > 0){
					ol.css('margin-left', (ol.find('li').length/100|0) + 'px');
				}

				othis.find('li').each(function(){
					$(this).html('<span>'+$(this).html()+'</span>')
				})
			})
		},
		aniBox: function(opts){
			if(!$(this).length)	return
			var inits = {
				aniName: 'z-anim-upbit',
				aniTime: 500
			}
			var options = $.extend({}, inits, opts)
			aniBox($(this), options)
		},
		swipeleft: function(cb){
			swipe('left', $(this), cb)
		},
		swiperight: function(cb){
			swipe('right', $(this), cb)
		},
		mobileNav: function(opts){
			if(!$(this).length)	return
			var nav = $(this).first()
			var inits = {
				item: '.z-nav-item',
				logo: 'z-logo',
				style: null,	 	// {}
				child: '.z-dropdown-menu',
				toggle: true,
				class: '',
				menu: '<span class="z-icon z-pull-right" style="line-height:1em;color:#fff;padding: 13px;font-size: 24px;">&#xe60d;</span>'
			}
			var options = getOpts(nav, inits, opts)
			mobileNav(nav, options)
		}
	})

	function mobileNav(nav, opts){
		var can = true
		var items = nav.find(opts.item)
		var fixNav = $('<div class="z-nav z-nav-tree z-nav-mobile '+opts.class+'"></div>')
		var logo = ''
		var menu = $(opts.menu)
		items.each(function(){
			if($(this).hasClass(opts.logo.replace(',','')))	logo = $(this)
			fixNav.append($(this).addClass('z-nav-item'))
		})
		nav.html(logo).append(menu).addClass('z-nav-fixed-top').removeClass('z-action-mobilenav')

		fixNav.find(opts.child).each(function(){
			$(this).removeClass(opts.child.substr(1)).addClass('z-nav-child').parent().removeClass('z-dropdown')
		})
		if(opts.style)	fixNav.css(style)
		if(opts.toggle)	fixNav.addClass('z-nav-toggle')
		nav.after(fixNav)
		menu.on('click', function(){
			if(!can)	return false
			can = false
			var _this = $(this)
			if(fixNav.is(':hidden')){
				$.createShade(function(){
					fixNav.css('zIndex', z.zIndex()).show().animate({'left':0},300);
					_this.html('&times');
					can = true
				}, function(){
					menu.click()
				}, '.12')
				nav.css('zIndex', z.zIndex())
			}else{
				fixNav.animate({'left':'-200px'},300,function(){
					fixNav.hide()
					_this.html('&#xe60d;')
					$('.z-shade').fadeOut(200, function(){
						$('.z-shade').remove()
						$('body').removeClass('overflow')
						can = true
					})
				});
			}
		})
		$(d).swiperight(function(){
			if(!fixNav.is(':hidden'))	return false
			menu.click()
		})
		$(d).swipeleft(function(){
			if(fixNav.is(':hidden'))	return false
			menu.click()
		})
	}

	function swipe(type, ele, cb){
		var sx = 0
		var sy = 0
		var ev = event
		var can = false
		var timer = null
		ele.on('touchstart',function(e){
			e.stopPropagation()
            ev = e.originalEvent.touches[0]
            sx = ev.clientX
            sy = ev.clientY
            can = true
            timer = setTimeout(function(){
            	can = false
            },300)
        })
        ele.on('touchend',function(e){
        	e.stopPropagation()
            ev = e.originalEvent.changedTouches[0]
            var offset = 'left' === type ? (sx - ev.clientX) : (ev.clientX - sx)
            if(offset > 50 && Math.abs(sy - ev.clientY) < 50){
            	if(cb && can)	cb()
            	if(timer)	clearTimeout(timer)
            }
        })
	}

	function resetForm(boxs){
		boxs.each(function(i){
			var isCheck = this.checked
			var zname = this.name
			var zid = zname + i.toString()
			var type = this.type
			if($(this).zdata('action') === 'noreform'){
				$(this).show()
				return true
			}
			if($.type($(this).zdata('name')) === 'undefined'){
				var html = '<div class="z-unselect z-form-'+type+function(){return isCheck?" z-active":""}()+'" zdata-id="'+zid+'" zdata-name="'+zname+'"><i class="z-anim z-icon">'+function(){return type=='radio'?"&#xe998;":"&#xe615;"}()+'</i><span>'+this.title+'</span></div>'
				$(this).zdata('id', zid)
				$(this).after(html)
			}
		})
	}

	function include(type, files, cb){
		if(!files)	return
		if($.type(files) === 'string'){
			files = [files]
		}
		if($.type(files) !== 'array')	return
		var node,suffix = '.'+type
		var i = 0
		require()
		function require(){
			if(i<files.length){
				var file = files[i]
				if(getZsrc(file)){
					onload()
					return
				}
				if(file.substr(-(suffix.length)) !== suffix){
					file += suffix
				}
				if(file.indexOf('http://') === -1 && file.indexOf('https://') === -1){
					file = z.root_path + file
				}
				if('js' === type){
					node = d.createElement('script')
					node.src = file
				}else if('css' === type){
					node = d.createElement('link')
					node.rel = 'stylesheet'
					node.href = file
				}
				d.head.appendChild(node)
				node.onload = function(){
					onload()
				}
			}
		}
		function onload(){
			if(i === files.length-1){
				if(cb)	cb()
			}else{
				i++
				require()
			}
		}
	}

	function getZsrc(src){
		var pt = d.getElementsByTagName('script')
		for(var i=0;i<pt.length;i++){
			var s = pt[i]
			if(src && s.src.indexOf(src) !== -1){
				return true
			}	
			if(!src && s.src.indexOf('js/z.js') !== -1){
				return s.src.replace('z.js','')
			}else if(!src && s.src.indexOf('js/z.min.js') !== -1){
				return s.src.replace('z.min.js','')
			}
		}
		return false
	}

	function pro_firm(type, content, title, btns, cb){
		if($.type(title) === 'array'){
			btns = title
			cb = btns
			title = ''
		}
		if($.type(title) === 'function'){
			cb = title
			title = ''
			btns = []
		}
		if($.type(btns) === 'function'){
			cb = btns
			btns = []
		}
		if($.type(btns) === 'string'){
			btns = ['取消', btns]
		}else if(!btns || !btns.length){
			btns = ['取消', '确认']
		}else if(btns.length < 2){
			btns.unshift('取消')
		}
		if('confirm' === type){
			title = title || z.title.confirm
		}else if('prompt' === type){
			content = '<input type="text" autofocus class="z-input" placeholder="'+title+'" />'
			title = title || z.title.prompt
		}
		btns = btns.slice(0,2)
		showMsg(type, content || '', title, btns, function(obj, i){
			doClose(obj, function(){
				if(i > 0){
					if(cb)	cb(obj.find('.z-input').val())
				}
			})
		})
	}

	function getAnim(){
		return z.anims[Math.floor(Math.random()*(z.anims.length-1))]
	}

	function showMsg(type, content, title, btns, cb){
		var html = ''
		var htmls = []
		var modal_arr = ['alert', 'confirm', 'prompt', 'open']
		if($.inArray(type, modal_arr) !== -1){
			if($.type(title) === 'array'){
				btns = title
				cb = btns
				title = ''
			}
			htmls = [
				'<div class="z-modal '+function(){return 'open'===type?'':'z-modal-sm'}()+' z-anim-ms3 '+getAnim()+'">',
				    '<div class="z-modal-content z-move">',
				      '<div class="z-modal-header z-move-title z-action-move">',
				        '<button type="button" class="z-close z-action-close">&times;</button>',
				        '<h4 class="z-modal-title">'+title+'</h4>',
				      '</div>',
				      '<div class="z-modal-body">',
				        content,
				      '</div>',
				      '<div class="z-modal-footer z-text-center">'
			]
			for(var i=0;i<btns.length;i++){
				if(btns[i].length)
					htmls.push('<button type="button" class="z-btn z-btn-'+function(){return 0===i?'default':'primary'}()+'">'+btns[i]+'</button>')
			}
			htmls.push('</div></div></div>')
			html = $(htmls.join(''))
			$.createShade(function(){
				$('body').append(html)
				html.css('zIndex',z.zIndex()).show()
				var box = html.find('.z-modal-content')
				var win = box.innerWidth()
				var hig = box.innerHeight()
				var ww = $(w).innerWidth()
				var wh = $(w).innerHeight()
				box.css({
					'margin': 0,
					'top': (wh-hig)/3>0?(wh-hig)/3:0,
					'left': (ww-win)/2>0?(ww-win)/2:0
				})
				html.find('.z-btn').on('click', function(){
					cb(html, $(this).index())
				})
			})
		}else if(type === 'msg'){
			$('.z-msg').remove()
			html = $('<div class="z-msg z-anim-scaleSpring z-anim-ms3" style="z-index:'+z.zIndex()+'">'+content+'</div>')
			$('body').append(html)
			html.css({
				marginTop: -html.innerHeight()/2,
				marginLeft: -html.innerWidth()/2
			})
			setTimeout(function(){
				html.hide(150, function(){
					html.remove()
				})
			},4000)
		}else{
			var color = title
			htmls = [
				'<div class="z-alert z-alert-'+color+' z-alert-box z-alert-dismissible z-anim-downbit z-anim-ms3" style="z-index:'+z.zIndex()+'">',
			      '<button type="button" class="z-close z-action-close">&times;</button>',
			      content,
			    '</div>'
			]
			html = $(htmls.join(''))
			$('body').append(html)
			setTimeout(function(){
				html.slideUp(150, function(){
					html.remove()
				})
			},5000)
		}
	}


	function doClose(box, cb, rm){
		box.slideUp(200, function(){
			if($('.z-shade').length){
				$('.z-shade').fadeOut(200, fn)
			}else{
				fn()
			}
		})
		function fn(){
			if($.type(rm) === 'undefined')	rm = true
			if(bool(rm))	box.remove()
			$('.z-shade').remove()
			$('body').removeClass('overflow')
			if(cb)	cb()
		}
	}

	function button(ele, type){
		if(type === 'loading'){
			if(ele.hasClass('z-disabled'))	return false
			var txt = ele.zdata('loading-text') || 'loading'
			txt = '<span class="z-anim-rotate z-icon" style="display: inline-block; margin-right: 5px; padding:1px 0 0 0">&#xe62d;</span>' + txt
			ele.attr('disabled', 'true').addClass('z-disabled').attr('zdata-old-text', ele.text()).html(txt)
		}else if(type === 'reset'){
			if(!ele.hasClass('z-disabled'))	return false
			var txt = ele.zdata('old-text') || 'resetBtn'
			ele.removeAttr('disabled').removeAttr('zdata-old-text').removeClass('z-disabled').html(txt)
		}
	}

	function tips(ele, opts){
		var timer = null
		$('body').off('mouseenter').on('mouseenter', ele, function(){
			var title = this.title
			var ztitle = $(this).zdata('title')
			if(!title && !ztitle)	return
			if(title){
				$(this).zdata('title', title)
				$(this).removeAttr('title')
			}
			opts = getOpts($(this), opts)
			var wid = this.offsetWidth
			var pos = $(this).offset()
			var content = $(this).zdata('title')
			var html = ''
			var htmls = [
				'<div class="z-tipsbox z-anim-scale z-anim-ms2" style="z-index:'+z.zIndex()+';top:'+(pos.top-40)+'px;left:'+function(){
					var left = pos.left
					if(wid < 17)	left = left - 17
					if(left < 0)	left = 0
					return left
				}()+'px">',
				'<div class="z-tipsbox-content"'+function(){
					var str = 'style="'
					if(opts.bgColor !== 'black'){
						str += 'background-color: '+opts.bgColor+';'
					}
					if(opts.txtColor !== 'white'){
						str += 'color: '+opts.txtColor+';'
					}
					str += '"'
					return str
				}()+'>',
				content,
				'</div><i style="border-right-color:'+opts.bgColor+'"></i>',
				'</div>'
			]
			html = $(htmls.join(''))
			$('.z-tipsbox').remove()
			$('body').append(html)
			var hig = pos.top - html.innerHeight() - 10
			if((hig - $(d).scrollTop()) < 0){
				html.css('top', pos.top + $(this).innerHeight() + 10).find('i').css({top: '-8px', bottom: 'auto'})
			}else{
				html.css('top', hig)
			}
			if(bool(opts.autoDie)){
				if(timer)	clearTimeout(timer)
				timer = setTimeout(function(){
					html.remove()
				},5000)
			}
		})
		$('body').off('mouseleave').on('mouseleave', ele, function(){
			if(timer)	clearTimeout(timer)
			$('.z-tipsbox').remove()
		})
	}

	function move(ele, opts){
		var isDown = false
		var mtop = 0
		var mleft = 0
		var wid = 0
		var hig = 0
		var ww = $(w).innerWidth()
		var wh = $(w).innerHeight()		
		$(w).on('mousemove', function(e){
			if(!isDown)	return
			var ev = e || event
			ev.stopPropagation()
			ev.preventDefault()
			var box = $(ele).parents(opts.box)
			var mpos = mousePosition(ev)
			var left = mpos.left - mleft
			var top = mpos.top - mtop
			if(top < opts.top)	top = opts.top
			if(left < opts.left)	left = opts.left
			if(top > (wh-hig-opts.bottom))	top = wh-hig-opts.bottom
			if(left > (ww-wid-opts.right))	left = ww-wid-opts.right
			box.css({
				'left': left,
				'top': top
			})
		})

		$('body').on('mousedown', ele, function(e){
			isDown = true
			var ev = e || event
			var box = $(this).parents(opts.box)
			var pos = box.position()
			var mpos = mousePosition(ev)
			wid = box.innerWidth()
			hig = box.innerHeight()
			mtop = mpos.top - pos.top
			mleft = mpos.left - pos.left
		})

		$('body').on('mouseup', ele, function(){
			isDown = false
		})
	}

	function mousePosition(ev){
		return {
			top: ev.clientY,
			left: ev.clientX
		}
	}

	function slider(ele, opts){
		opts = getOpts(ele, opts)
		var width = ele.innerWidth()
		var item = ele.find(opts.item)
		var items = ele.find(opts.items)
		var prev = ele.find(opts.prev)
		var next = ele.find(opts.next)
		var leg = item.length
		var timer = null
		var canMove = true
		var index = 0
		var dots = null
		var box = null
		var mar = -width

		if(bool(opts.dots)){
			ele.append('<div class="z-dots">'+function(){
				var li = ''
				for(var i=0;i<leg;i++){
					if(0===i)	li += '<a href="javascript:;" class="z-active"></a>'
					else	li += '<a href="javascript:;"></a>'
				}
			return li
			}()+'</div>')

			dots = ele.find('.z-dots>a')

			if(bool(opts.keys)){
				dots.on('click', function(){
					movement(- ($(this).index() + 1) * width)
				})
			}
		}

		ele.width(width)

		if(leg > 1){
			var f = item.eq(0).clone()
			var l = item.eq(leg-1).clone()
			items.prepend(l)
			items.append(f)
			item = ele.find(opts.item)
			leg = item.length
			items.wrap('<div></div>')
			box = items.parent()
			box.css('marginLeft', mar)
		}else	return

		item.width(width)
		items.height(item.innerHeight()).width(leg*width)

		timer = setInterval(movement, opts.delay)

		if(z.isMobile){
			var touch = null
			var sx = 0
			var direction = 'left'
			var move = 0
			prev.remove()
			next.remove()
			ele.on('touchmove', function(e) {
				e.stopPropagation()
		　　　　e.preventDefault()
				move = touch.clientX - sx
		        touch = e.originalEvent.changedTouches[0]
		        box.css('marginLeft', mar + move)
			})

			ele.on('touchstart',function(e){
		　　　　e.preventDefault()
				e.stopPropagation()
	            touch = e.originalEvent.touches[0]
	            sx = touch.clientX
	            clearInterval(timer)
	        })
	        ele.on('touchend',function(e){
		　　　　e.preventDefault()
	        	e.stopPropagation()
	            touch = e.originalEvent.changedTouches[0]
				if(touch.clientX - sx > 0)	direction = 'right'
				else 	direction = 'left'
	            movement(direction)
	            timer = setInterval(movement, opts.delay)
	        })
		}else{
			ele.on('mouseenter', function(){
				clearInterval(timer)
			})
			ele.on('mouseleave', function(){
				timer = setInterval(movement, opts.delay)
			})

			prev.on('click', function(){
				clearInterval(timer)
				if(canMove)	movement('left')
			})
			next.on('click', function(){
				clearInterval(timer)
				if(canMove)	movement('right')
			})
		}


		function movement(direction){
			canMove = false
			direction = direction || 'left'
			if(direction === 'left'){
				if(Math.abs(mar) >= (leg-2)*width){
					mar = 0
					box.css({'marginLeft': mar})
				}
				mar = mar - width
			}else if(direction === 'right'){
				if(Math.abs(mar) <= width){
					mar = -(leg-1)*width
					box.css({'marginLeft': mar})
				}
				mar = mar + width
			}else{
				mar = direction
			}
			box.animate({'marginLeft': mar}, opts.speed, function(){
				index = parseInt((Math.abs(mar)-width)/width)
				if(dots)	dots.removeClass('z-active').eq(index).addClass('z-active')
				if(opts.complete)	opts.complete(item.eq(index+1))
				canMove = true
			})
		}
	}

	function supportCss3(style) { 
		var prefix = ['webkit', 'Moz', 'ms', 'o'], 
		i, 
		humpString = [], 
		htmlStyle = d.documentElement.style, 
		_toHumb = function (string) { 
			return string.replace(/-(\w)/g, function ($0, $1) {
				return $1.toUpperCase(); 
			}); 
		}; 
		 
		for (i in prefix) 
			humpString.push(_toHumb(prefix[i] + '-' + style)); 
		 
		humpString.push(_toHumb(style)); 
		 
		for (i in humpString) 
			if (humpString[i] in htmlStyle) return true; 
		 
		return false; 
	}

	function aniBox(ele, opts){
		var isEnd = false
		ele.each(function(){
			var top = $(this).offset().top
			aniBoxTops.push(top)
			$(this).css('opacity', 0).zdata('top', top)
			if(!$(this).zdata('aniName')){
				$(this).zdata('aniName', opts.aniName?opts.aniName:getAnim())
			}
			if(!$(this).zdata('aniTime')){
				$(this).zdata('aniTime', opts.aniTime)
			}
		})
		var maxHig = Math.max.apply({}, aniBoxTops)
		maxHig = Math.min(maxHig, ($(d).innerHeight() - $(w).innerHeight()))

		doing()
		$(w).on('scroll', doing)
		function doing(){
			if(isEnd)	return
			var dtop = $(d).scrollTop()
			if(dtop <= maxHig){
				var queue = []
				ele.each(function(){
					var _this = $(this)
					if(_this.zdata('aniName') && parseInt(_this.zdata('top')) <= (dtop+$(w).height())){
						queue.push(_this)
					}
				})
				if(queue.length){
					var i = 0
					var timer = setInterval(function(){
						if(i>=queue.length){
							clearInterval(timer)
							return
						}
						var item = queue[i]
						item.addClass(item.zdata('aniName')).addClass('z-anim-ms'+parseInt(item.zdata('aniTime'))/100).removeAttr('zdata-top').removeAttr('zdata-aniName').removeAttr('zdata-aniTime').animate({'opacity': 1}, 300)
						++i
					}, 100)					
				}
			}else{
				isEnd = true
			}
		}
	}

	function getOpts(ele, inits, opts){
		var options = inits
		if(opts)	options = $.extend({}, inits, opts)
		
		$.each(options, function(key, val) {
			if($.type(ele.zdata(key)) !== 'undefined')	options[key] = ele.zdata(key)
		});
		return options
	}

	function bool(type){
		if($.type(type) === 'undefined')	return false
		else if($.type(type) === 'boolean')	return type
		if(w.JSON){
			return JSON.parse(type)
		}else{
			return eval(type)
		}
	}

	function isMobile(){
		return navigator.userAgent.match(/mobile/i)
	}


	$(function(){

		// 代码修饰器
		$('.z-action-code').code();

		// 日期选择器
		$('.z-action-datepicker').datepicker()

		if(!z.isMobile){
			// tips
			$('.z-action-tips').tips()

			// 拖动组件
			$('.z-action-move').move()
		}else{
			// 移动端替换导航
			$('.z-action-mobilenav').mobileNav()
		}

		// 返回顶部
	    $('.z-action-gotop').goTop()
		
		// 幻灯
		$('.z-action-slider').slider()
		
		// 动态进入
		$('.z-action-anibox').aniBox()
		
		// 替换单选框和复选框
		$.resetForm()

		// 单选框
		$('body').on('click','.z-form-radio',function(){
			if($(this).hasClass('z-active'))	return
			var zid = $(this).zdata('id')
			var zname = $(this).zdata('name')
			$('.z-form-radio[zdata-name="'+zname+'"]').removeClass('z-active')
			$(this).addClass('z-active')
			$('input[type="radio"][name="'+zname+'"]').each(function(){
				this.checked = $(this).zdata('id') === zid
			})
		})

		// 复选框
		$('body').on('click','.z-form-checkbox',function(){
			var zid = $(this).zdata('id')
			$(this).toggleClass('z-active')
			$('input[type="checkbox"][zdata-id="'+zid+'"]').attr('checked', $(this).hasClass('z-active'))
		})

		// modal
		$('body').on('click', '.z-action-modal', function(){
			var tar = $(this).zdata('target')
			if(!tar || !tar.length || !$(tar).length)	return
			$(tar).modal()
		})

		// 关闭按钮
		$('body').on('click', '.z-alert .z-action-close,.z-modal .z-action-close', function(){
			var _this = $(this)
			var box = null
			if(_this.parents('.z-alert').length){
				box = _this.parents('.z-alert')
			}else if(_this.parents('.z-modal').length){
				box = _this.parents('.z-modal')
			}
			if(box)	doClose(box, null, _this.zdata('remove'))
		})

		// 上一页
		$('body').on('click', '.z-action-back', function(){
			$.back()
		})

		// 刷新
		$('body').on('click', '.z-action-reload', function(){
			$.reload()
		})

		// 树形导航
		$('body').on('click','.z-nav-tree .z-nav-item',function(e){
			var _this = $(this)
			var ev = e || event
			ev.stopPropagation() 
			if(_this.hasClass('z-active')){
				_this.removeClass('z-active')
			}else{
				var pnav = _this.parents('.z-nav-tree')
				if(pnav.hasClass('z-nav-toggle')){
					pnav.find('.z-nav-item').removeClass('z-active')
				}
				_this.addClass('z-active')
			}
		})

		// 下拉导航
		$('body').on('click', '.z-dropdown', function(e){
			var _this = $(this)
			var ev = e || event
			ev.stopPropagation()
			if(_this.hasClass('z-active'))	_this.removeClass('z-active')
			else{
				$('.z-dropdown').removeClass('z-active')
				_this.addClass('z-active')
			}
		})

		// tabs
		$('body').on('click', '.z-tab-title li', function(){
			var _this = $(this)
			var lis = _this.parent().find('li')
			var items = _this.parents('.z-tab').find('.z-tab-content .z-tab-item')
			var index = _this.index()
			if(_this.hasClass('z-active'))	return
			lis.removeClass('z-active')
			_this.addClass('z-active')
			items.removeClass('z-show').eq(index).addClass('z-show')
		})

		// 固定导航
		if($('.z-nav.z-nav-fixed-top').length)	$('body').css('paddingTop', '70px')
		if($('.z-nav.z-nav-fixed-bottom').length)	$('body').css('paddingBottom', '70px')

		// 重置
		$(d).on('click',function(){
			$('.z-dropdown').removeClass('z-active')
		})
		$(d).on('click', '.z-disabled', function(e){
			var ev = e || event
			ev.stopPropagation()
			ev.preventDefault()
		})

	})

	w.z = z
})(window, document, jQuery)
