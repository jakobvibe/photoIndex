function pswp() {
};
pswp.close = function() {};
$(function(){

	var filemanager = $('.filemanager'),
		breadcrumbs = $('.breadcrumbs'),
		fileList = filemanager.find('.data');
		
	var imageFileTypes = ['jpg', 'jpeg', 'png', 'gif'];

	// Start by fetching the file data from scan.php with an AJAX request

	$.get('directorylist.json', function(data) {

		var response = [data],
			currentPath = '',
			breadcrumbsUrls = [];

		var folders = [],
			files = [];

		// This event listener monitors changes on the URL. We use it to
		// capture back/forward navigation in the browser.

		$(window).on('hashchange', function(){

			goto(window.location.hash);
			pswp.close();

			// We are triggering the event. This will execute 
			// this function on page load, so that we show the correct folder:

		}).trigger('hashchange');


		// Hiding and showing the search box

		filemanager.find('.search').click(function(){

			$(this).find('span').hide();
			$(this).find('input[type=search]').show().focus();

		});


		// Listening for keyboard input on the search field.
		// We are using the "input" event which detects cut and paste
		// in addition to keyboard input.

		var search = filemanager.find('.search input').on('input', function(e){

			folders = [];
			files = [];

			var value = this.value.trim();

			if(value.length) {

				filemanager.addClass('searching');

				// Update the hash on every key stroke
				window.location.hash = 'search=' + value.trim();

			}

			else {

				filemanager.removeClass('searching');
				window.location.hash = encodeURIComponent(currentPath);

			}

		}).on('keyup', function(e){

			// Clicking 'ESC' button triggers focusout and cancels the search

			if(e.keyCode == 27) {

				$(this).trigger('focusout');

			}

		}).focusout(function(e){

			// Cancel the search
			

			if (window.location.hash.length > 0) {
				window.location.hash = encodeURIComponent(currentPath);
			}
			$(this).val('');
			$(this).hide();
			$(this).parent().find('span').show();

			filemanager.removeClass('searching');

		});
		
		$('.clear-search').click(function(e){
			search.trigger('focusout');
		});


		// Clicking on folders

		fileList.on('click', 'li.folders', function(e){
			e.preventDefault();

			var nextDir = $(this).find('a.folders').attr('href');

			if(filemanager.hasClass('searching')) {

				// Building the breadcrumbs

				breadcrumbsUrls = generateBreadcrumbs(nextDir);

				filemanager.removeClass('searching');
				filemanager.find('input[type=search]').val('').hide();
				filemanager.find('span').show();
			}
			else {
				breadcrumbsUrls.push(nextDir);
			}

			window.location.hash = encodeURIComponent(nextDir);
			currentPath = nextDir;
		});

		// Clicking on breadcrumbs

		breadcrumbs.on('click', 'a', function(e){
			e.preventDefault();

			var index = breadcrumbs.find('a').index($(this)),
				nextDir = breadcrumbsUrls[index];

			breadcrumbsUrls.length = Number(index);

			window.location.hash = encodeURIComponent(nextDir);

		});


		// Navigates to the given hash (path)

		function goto(hash) {

			hash = decodeURIComponent(hash).slice(1).split('=');
			if (hash.length) {
				var rendered = '';
				fileList.removeClass('animated').addClass('animated-switch');
				// if hash has search in it

				if (hash[0] === 'search') {

					filemanager.addClass('searching');
					rendered = searchData(response, hash[1].toLowerCase());
					if (rendered.length) {
						currentPath = hash[0];
						render(rendered);
					}
					else {
						render(rendered);
					}

				}

				// if hash is some path

				else if (hash[0].trim().length) {
					filemanager.removeClass('searching');
					rendered = searchByPath(hash[0]);

					if (rendered.length) {

						currentPath = hash[0];
						breadcrumbsUrls = generateBreadcrumbs(hash[0]);
						render(rendered);

					}
					else {
						currentPath = hash[0];
						breadcrumbsUrls = generateBreadcrumbs(hash[0]);
						render(rendered);
					}

				}

				// if there is no hash

				else {
					filemanager.removeClass('searching');
					currentPath = data.path;
					breadcrumbsUrls.push(data.path);
					render(searchByPath(data.path));
				}
			}
		}

		// Splits a file path and turns it into clickable breadcrumbs

		function generateBreadcrumbs(nextDir){
			var path = nextDir.split('/').slice(0);
			for(var i=1;i<path.length;i++){
				path[i] = path[i-1]+ '/' +path[i];
			}
			return path;
		}


		// Locates a file by path

		function searchByPath(dir) {
			var path = dir.split('/'),
				demo = response,
				flag = 0;

			for(var i=0;i<path.length;i++){
				for(var j=0;j<demo.length;j++){
					if(demo[j].name === path[i]){
						flag = 1;
						demo = demo[j].items;
						break;
					}
				}
			}

			demo = flag ? demo : [];
			return demo;
		}


		// Recursively search through the file tree

		function searchData(sData, searchTerms, result) {
			if (typeof result === 'undefined') {
				result = {
					folders : [],
					files: []
				};
			}
			sData.forEach(function(d){
				if(d.type === 'folder') {

					result = searchData(d.items,searchTerms, result);

					if(d.name.toLowerCase().match(searchTerms)) {
						result.folders.push(d);
					}
				}
				else if(d.type === 'file') {
					if(d.name.toLowerCase().match(searchTerms)) {
						result.files.push(d);
					} 
					if(typeof d.keywords == 'string' && d.keywords.toLowerCase().match(searchTerms)) {
						result.files.push(d);
					}
				}
			});
			return result;
		}


		// Render the HTML for the file manager

		function render(renderData) {
			var scannedFolders = [],
				scannedFiles = [];

			if(Array.isArray(renderData)) {

				renderData.forEach(function (d) {

					if (d.type === 'folder') {
						scannedFolders.push(d);
					}
					else if (d.type === 'file') {
						scannedFiles.push(d);
					}

				});

			}
			else if(typeof renderData === 'object') {

				scannedFolders = renderData.folders;
				scannedFiles = renderData.files;

			}


			// Empty the old result and make the new one

			fileList.empty().hide();

			if(!scannedFolders.length && !scannedFiles.length) {
				filemanager.find('.nothingfound').show();
			}
			else {
				filemanager.find('.nothingfound').hide();
			}

			if(scannedFolders.length) {

				scannedFolders.forEach(function(f) {

					var itemsLength = f.items.length,
						name = escapeHTML(f.name),
						icon = '<span class="mime-icon folder"></span>';

					if(itemsLength) {
						icon = '<span class="mime-icon folder full"></span>';
					}

					if(itemsLength == 1) {
						itemsLength += ' element';
					}
					else if(itemsLength > 1) {
						itemsLength += ' elementer';
					}
					else {
						itemsLength = 'Tom';
					}

					var folder = $('<li class="folders"><a href="'+ f.path +'" title="'+ f.path +'" class="folders">'+icon+'<span class="name">' + name + '</span> <span class="details">' + itemsLength + '</span></a></li>');
					folder.appendTo(fileList);
				});

			}

			if(scannedFiles.length) {

				scannedFiles.forEach(function(f) {

					var fileSize = bytesToSize(f.size),
						name = escapeHTML(f.name),
						path = f.path,
						url = path,
						thumb = f.thumbnail ? f.thumbnail : url,
						artist = f.artist,
						description = f.imageDescription == 'N/A' ? '' : f.imageDescription,
						keywords = f.keywords,
						fileType = name.split('.'),
						width = f.width,
						height = f.height,
						isImage = false,
						icon = '<span class="mime-icon file"></span>';

					fileType = fileType[fileType.length-1];
					isImage = $.inArray(fileType, imageFileTypes) !== -1;
					artist = artist ? artist : 'Ukendt';
					icon = '<span class="mime-icon file f-'+fileType+'">.'+fileType+'</span>';

					var file = $('<li class="files"><a href="'+ url+'" title="'+ path +'" class="files" target="_blank">'+icon+'<div class="info"><p class="description">'+description+'</p><span class="artist"><i class="icon icon-camera2"></i> '+ artist +'</span><span class="name">'+ name +'</span></div></a></li>');
					var $link = file.find('a.files');
					if (isImage) {
						file.addClass('photo');
						$link.css({
							'background-image' : 'url("' + thumb + '")'
						});
					}
					if (!description || description == '' || description == "") {
						file.find('.description').hide();
					}
					
					file.attr({
						'data-name' : name,
						'data-keywords' : keywords,
						'data-fileSize' : fileSize,
						'data-fileType' : fileType,
						'data-isImage' : isImage,
						'data-artist' : artist,
						'data-copyright' : f.copyright,
						'data-cameramaker' : f.camera_maker,
						'data-cameramodel' : f.camera_model,
						'data-exposure' : f.exposure,
						'data-aperture' : f.aperture,
						'data-iso' : f.iso,
						'data-filesize' : f.size,
						'data-timestamp' : f.date,
						'data-description' : description,
						'data-keywords' : keywords,
						'data-url' : url,
						'data-dimensions' : width +'x'+ height
					});
					file.appendTo(fileList);
				});

			}


			// Generate the breadcrumbs

			var url = '';

			if(filemanager.hasClass('searching')){
				hash = decodeURIComponent(window.location.hash).slice(1).split('=');
				debugger;
				url = '<span>Søgning: '+ hash[1] +'</span>' ;
				fileList.removeClass('animated');

			}
			else {

				fileList.addClass('animated');

				breadcrumbsUrls.forEach(function (u, i) {

					var name = u.split('/');

					if (i !== breadcrumbsUrls.length - 1) {
						url += '<a href="'+u+'"><span class="folderName">' + name[name.length-1] + '</span></a> <i class="arrow">/</i> ';
					}
					else {
						url += '<span class="folderName">' + name[name.length-1] + '</span>';
					}

				});

			}

			breadcrumbs.text('').append(url);


			// Show the generated elements

			fileList.show();

		}


		// This function escapes special html characters in names

		function escapeHTML(text) {
			return text.replace(/\&/g,'&amp;').replace(/\</g,'&lt;').replace(/\>/g,'&gt;');
		}


		// Convert file sizes from bytes to human readable units

	});
});
function bytesToSize(bytes) {
	var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
	if (bytes == 0) return '0 Bytes';
	var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
	return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}

function loadInfo($file) {
	$info = $('.photo-info');
	$info.find('.name.text').text($file.data('name'));
	if ($file.data('description') && $file.data('description') != "") {
		$info.find('.description.text').text($file.data('description'));
		$info.find('.description').show();
	} else {
		$info.find('.description').hide();
	}
	$info.find('.file-info.text .filename').text($file.data('name'));
	$info.find('.file-info.text .megapixel').text(function() {
		var dimensions = [], width = 0, height = 0; 
		if (!$file.data('dimensions')) {
			return;
		}
		dimensions = $file.data('dimensions').split('x');
		width = dimensions[0];
		height = dimensions[1];
		size = Math.round((parseInt(width * height, 10) / 1024000) * 10) / 10;
		return size + 'MP';
	});
	$info.find('.file-info.text .dimensions').text($file.data('dimensions').replace('x',' × '));
	$info.find('.file-info.text .filesize').text(bytesToSize($file.data('filesize')));
	$info.find('.camera-info.text .model').text(function() {
		var words = [], maker = '', model = '', makerShortName = '', fullName = '';
		maker = $file.data('cameramaker') ? $file.data('cameramaker') : '';
		model = $file.data('cameramodel') ? $file.data('cameramodel') : '';
		words = maker.split(' ');
		if (maker != '' && words.length == 0) {
			makerShortName = maker;
		} else if (maker != '' && words.length > 0) {
			makerShortName = words[0];
		} else {
			makerShortName = '';
		}
		//The maker name is also present in the model name - do not append it
		if (makerShortName != '' && model.indexOf(makerShortName) !== -1) {
			fullName = model;
		} else {
			fullName = makerShortName + ' ' + model;
		}
		return fullName;
	});
	$info.find('.camera-info.text .aperture').text($file.data('aperture'));
	$info.find('.camera-info.text .exposure').text($file.data('exposure'));
	$info.find('.camera-info.text .iso').text(function(){
		if ($file.data('iso') && String($file.data('iso')).indexOf('ISO') === -1) {
			return 'ISO-'+$file.data('iso');
		} else {
			return $file.data('iso');
		}
	});
	$info.find('.author-info.text .author').text($file.data('artist'));
	$info.find('.author-info.text .copyright').text($file.data('copyright'));
	$info.find('.keywords.text').html(function() {
		var result = '';
		var keywords = $file.data('keywords');
		if (!keywords) {
			return;
		}
		var keywordArr = keywords.split(',');
		for (var i = 0; i < keywordArr.length; i++) {
			result += '<a class="badge keyword" href="#search='+ keywordArr[i] +'">' + keywordArr[i] + '</a>';
		}
		return result;
	});
}

var pswpElement = $('.pswp')[0];

var options = {
	index: 0,
	history: false
};

(function() {

		var initPhotoSwipeFromDOM = function(gallerySelector) {

			var parseThumbnailElements = function(el) {
				var thumbElements = $(el).closest('ul').find('.photo').get(),
					numNodes = typeof thumbElements.length === 'undefined' ? 1 : thumbElements.length,
					items = [],
					el,
					childElements,
					size,
					item;

				for(var i = 0; i < numNodes; i++) {
					el = thumbElements[i];
					// include only element nodes 
					if(el.nodeType !== 1) {
					  continue;
					}

					childElements = el.children;

					size = el.getAttribute('data-dimensions').split('x');

					// create slide object
					item = {
						src: el.getAttribute('data-url'),
						w: parseInt(size[0], 10),
						h: parseInt(size[1], 10),
						author: el.getAttribute('data-artist')
					};

					item.el = el; // save link to element for getThumbBoundsFn

					if(childElements.length > 0) {
					  item.msrc = childElements[0].getAttribute('href'); // thumbnail url
					  item.title = el.getAttribute('data-description'); // caption (contents of figure)
					}


					var mediumSrc = el.getAttribute('data-med');
					if(mediumSrc) {
						size = el.getAttribute('data-med-size').split('x');
						// "medium-sized" image
						item.m = {
							src: mediumSrc,
							w: parseInt(size[0], 10),
							h: parseInt(size[1], 10)
						};
					}
					// original image
					item.o = {
						src: item.src,
						w: item.w,
						h: item.h
					};

					items.push(item);
				}

				return items;
			};

			// find nearest parent element
			var closest = function closest(el, fn) {
				return el && ( fn(el) ? el : closest(el.parentNode, fn) );
			};

			var onThumbnailsClick = function(e) {
				e = e || window.event;
				e.preventDefault ? e.preventDefault() : e.returnValue = false;

				var eTarget = e.target || e.srcElement;

				var clickedListItem = closest(eTarget, function(el) {
					return el.tagName === 'A';
				});

				if(!clickedListItem) {
					return;
				}

				var clickedGallery = clickedListItem.parentNode;

				//var childNodes = clickedListItem.parentNode.childNodes,
				var childNodes = $(clickedListItem).closest('ul').find('.photo > a').get(),
					numChildNodes = childNodes.length,
					nodeIndex = 0,
					index;
				for (var i = 0; i < numChildNodes; i++) {
					if(childNodes[i].nodeType !== 1) { 
						continue; 
					}
					
					if(childNodes[i] === clickedListItem) {
						index = nodeIndex;
						break;
					}
					nodeIndex++;
				}

				if(index >= 0) {
					openPhotoSwipe( index, clickedGallery );
				}
				return false;
			};

			var photoswipeParseHash = function() {
				var hash = window.location.hash.substring(1),
				params = {};

				if(hash.length < 5) { // pid=1
					return params;
				}

				var vars = hash.split('&');
				for (var i = 0; i < vars.length; i++) {
					if(!vars[i]) {
						continue;
					}
					var pair = vars[i].split('=');	
					if(pair.length < 2) {
						continue;
					}			
					params[pair[0]] = pair[1];
				}

				if(params.gid) {
					params.gid = parseInt(params.gid, 10);
				}

				return params;
			};

			var openPhotoSwipe = function(index, galleryElement, disableAnimation, fromURL) {
				var pswpElement = document.querySelectorAll('.pswp')[0],
					options,
					items;

				items = parseThumbnailElements(galleryElement);

				// define options (if needed)
				options = {

					galleryUID: galleryElement.getAttribute('data-pswp-uid'),

					getThumbBoundsFn: function(index) {
						// See Options->getThumbBoundsFn section of docs for more info
						var thumbnail = items[index].el.children[0],
							pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
							rect = thumbnail.getBoundingClientRect(); 

						return {x:rect.left, y:rect.top + pageYScroll, w:rect.width};
					},

					addCaptionHTMLFn: function(item, captionEl, isFake) {
						if(!item.title) {
							captionEl.children[0].innerText = '';
							return false;
						}
						captionEl.children[0].innerHTML = item.title +	'<br/><small>Photo: ' + item.author + '</small>';
						return true;
					},
					
				};


				if(fromURL) {
					if(options.galleryPIDs) {
						// parse real index when custom PIDs are used 
						// http://photoswipe.com/documentation/faq.html#custom-pid-in-url
						for(var j = 0; j < items.length; j++) {
							if(items[j].pid == index) {
								options.index = j;
								break;
							}
						}
					} else {
						options.index = parseInt(index, 10) - 1;
					}
				} else {
					options.index = parseInt(index, 10);
				}

				// exit if index not found
				if( isNaN(options.index) ) {
					return;
				}



				var radios = document.getElementsByName('gallery-style');
				for (var i = 0, length = radios.length; i < length; i++) {
					if (radios[i].checked) {
						if(radios[i].id == 'radio-all-controls') {

						} else if(radios[i].id == 'radio-minimal-black') {
							options.mainClass = 'pswp--minimal--dark';
							options.barsSize = {top:0,bottom:0};
							options.captionEl = false;
							options.fullscreenEl = false;
							options.shareEl = false;
							options.bgOpacity = 0.85;
							options.tapToClose = true;
							options.tapToToggleControls = false;
						}
						break;
					}
				}

				if(disableAnimation) {
					options.showAnimationDuration = 0;
				}
				
				options.history = false;

				// Pass data to PhotoSwipe and initialize it
				pswp = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);

				// see: http://photoswipe.com/documentation/responsive-images.html
				var realViewportWidth,
					useLargeImages = false,
					firstResize = true,
					imageSrcWillChange;

				pswp.listen('beforeResize', function() {

					var dpiRatio = window.devicePixelRatio ? window.devicePixelRatio : 1;
					dpiRatio = Math.min(dpiRatio, 2.5);
					realViewportWidth = pswp.viewportSize.x * dpiRatio;


					if(realViewportWidth >= 1200 || (!pswp.likelyTouchDevice && realViewportWidth > 800) || screen.width > 1200 ) {
						if(!useLargeImages) {
							useLargeImages = true;
							imageSrcWillChange = true;
						}
						
					} else {
						if(useLargeImages) {
							useLargeImages = false;
							imageSrcWillChange = true;
						}
					}

					if(imageSrcWillChange && !firstResize) {
						pswp.invalidateCurrItems();
					}

					if(firstResize) {
						firstResize = false;
					}

					imageSrcWillChange = false;

				});

				pswp.listen('gettingData', function(index, item) {
					item.src = item.o.src;
					item.w = item.o.w;
					item.h = item.o.h;
				});
				pswp.listen('beforeChange', function(index, item) {
					loadInfo($(pswp.currItem.el).closest('li'));
				});
				pswp.init();
			};

			// select all gallery elements
			var galleryElements = document.querySelectorAll( gallerySelector );
			for(var i = 0, l = galleryElements.length; i < l; i++) {
				galleryElements[i].setAttribute('data-pswp-uid', i+1);
				$(galleryElements[i]).on('click', '.photo',onThumbnailsClick);
			}

			// Parse URL and open gallery if it contains #&pid=3&gid=1
			var hashData = photoswipeParseHash();
			if(hashData.pid && hashData.gid) {
				openPhotoSwipe( hashData.pid,  galleryElements[ hashData.gid - 1 ], true, true );
			}
		};

		$(document).ready(function(){initPhotoSwipeFromDOM('.filemanager .data')});

	})();
