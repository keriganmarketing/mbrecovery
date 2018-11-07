/* 
	USP Pro JavaScript by Jeff Starr, Monzilla Media. v2.3
	Perpetual Copyright Monzilla Media, All Rights Reserved.
	Read-Only License for USP Pro, All Other Use Prohibited.
	Minify @ https://www.minifier.org/
*/

jQuery(document).ready(function($) {
	
	// "Agree to Terms" toggle
	$('.usp-agree-toggle').click(function(){ $('.usp-agree-terms').slideToggle(100); });
	
	// Disable form button after submit
	$('.usp-form').on('submit', function(e) {
		if (window.parsley) { 
			if ($(this).parsley().isValid()) {
				$('.usp-submit').css('cursor', 'wait');
				$('.usp-submit').attr('disabled', true);
			}
		} else {
			$('.usp-submit').css('cursor', 'wait');
			$('.usp-submit').attr('disabled', true);
		}
	});
	
	// "Add another" link : [usp_files method="" multiple="true"]
	$('.usp-files').each(function(index, value) { 
		var id = '#'+ $(this).attr('id');
		var n = parseInt($(id +' .usp-file-count').val());
		var x = parseInt($(id +' .usp-file-limit').val());
		var y = x - n;
		if (x == 1) {
			$(id +' .usp-add-another').hide();
		} else {
			$(id +' .usp-add-another').click(function(e) {
				e.preventDefault(); n++; y--;
				$('.usp-file-count').val(n);
				var $this = $(this);
				var $new = $this.parent().find('input:visible:last').clone().val('').attr('data-file', n).addClass('usp-clone');
				if ($new.hasClass('usp-input-custom')) {
					$new.attr('id', 'usp_custom_file_'+ n);
				} else {
					$new.attr('id', 'usp-files-'+ n);
				}
				if (y > 0) {
					$this.before($new.fadeIn(300).css('display', 'block'));
				} else if (y <= 0) {
					$this.before($new.fadeIn(300).css('display', 'block'));
					$this.hide();
				} else {
					$this.hide();
				}
			});
		}
	});
	
	// Preview selected images : [usp_files method="select" multiple="true"]
	$('.select-file.multiple').each(function(index, value) {
		$(this).on('change', function(event) {
			
			var any_window = window.URL || window.webkitURL;
			var div = '#'+ $(this).parent().parent().attr('id');
			var dom = $('#'+ $(this).attr('id'))[0];
			var files = dom.files;
			
			var preview = $(div +' input[name*="-preview"]').val();
			if (preview) return false;
			
			$(div +'.usp-preview').empty();
			
			for (var i = 0; i < files.length; i++) {
				var file_id = i + 1;
				var file_url = any_window.createObjectURL(files[i]);
				var file_ext = files[i].name.split('.')[files[i].name.split('.').length - 1].toLowerCase();
				var file_css = get_icon_url(file_url, file_ext);
				
				var append = true;
				var file_prv = $(div +' + .usp-preview .usp-preview-'+ file_id);
				if ($(file_prv).length) append = false;
				
				append_preview(div, file_id, file_url, file_css, append);
				window.URL.revokeObjectURL(files[i]);
			}
		});
	});
	
	// Preview selected images : [usp_files method="select" multiple="false"]
	$(document.body).on('change', '.select-file.single-file', function(event){
		var div_id = '#'+ $(this).parent().attr('id');
		var file_id = 1;
		
		var preview = $(div_id +' input[name*="-preview"]').val();
		if (preview) return false;
		
		if ($(this).val()) previewFiles(event, div_id, file_id);
	});
	
	// Preview selected images : [usp_files method="" multiple="false"]
	$(document.body).on('change', '.add-another.single-file', function(event){
		var div_id = '#'+ $(this).parent().attr('id');
		var file_id = 1;
		
		var preview = $(div_id +' input[name*="-preview"]').val();
		if (preview) return false;
		
		if ($(this).val()) previewFiles(event, div_id, file_id);
	});
	
	// Preview selected images : [usp_files method=""  multiple="true"]
	$(document.body).on('change', '.add-another.multiple', function(event){
		var div_id = '#'+ $(this).parent().parent().attr('id');
		var file_id = $(this).data('file');
		
		var preview = $(div_id +' input[name*="-preview"]').val();
		if (preview) return false;
		
		if ($(this).val()) previewFiles(event, div_id, file_id);
	});
	
	function previewFiles(event, div_id, file_id) {
		var files = event.target.files;
		var file_name = files[0].name;
		var any_window = window.URL || window.webkitURL;
		
		var file_url = any_window.createObjectURL(files[0]);
		var file_ext = file_name.split('.')[file_name.split('.').length - 1].toLowerCase();
		var file_css = get_icon_url(file_url, file_ext);
		
		var append = true;
		var file_prv = $(div_id +' + .usp-preview .usp-preview-'+ file_id);
		if ($(file_prv).length) append = false;
		
		append_preview(div_id, file_id, file_url, file_css, append);
	}
	
	function append_preview(div_id, file_id, file_url, file_css, append) {
		
		var prv_box = div_id +' + .usp-preview';
		var prv_file = div_id +' + .usp-preview .usp-preview-'+ file_id;
		
		var content = '<div class="usp-preview-'+ file_id +'"><a href="'+ file_url +'" title="Preview of file #'+ file_id +'" target="_blank" rel="noopener noreferrer"></a></div>';
		var styles = { 'background-image':'url('+ file_css +')', 'background-size':'cover', 'background-repeat':'no-repeat', 'background-position':'center center' };
		
		if (append == true) $(prv_box).append(content);
		else $(prv_file).replaceWith(content);
		
		$(prv_file).css(styles);
	}
	
	function get_icon_url(file_url, file_ext) {
		var url = '';
		if ($.inArray(file_ext, ['bmp','gif','jpe','jpeg','jpg','png','svg','tif','tiff']) > -1) {
			url = file_url;
			
		} else if ($.inArray(file_ext, ['3gp','avi','flv','mov','mp4','mpg','qt','swf','wmv']) > -1) {
			url = get_video_icon();
			
		} else if ($.inArray(file_ext, ['aac','aiff','alac','ape','flac','mid','mp3','ogg','wav','wma']) > -1) {
			url = get_audio_icon();
			
		} else if ($.inArray(file_ext, ['zip','rar']) > -1) {
			url = get_zip_icon();
			
		} else if ($.inArray(file_ext, ['pdf']) > -1) {
			url = get_pdf_icon();
		} else {
			url = get_other_icon();
		}
		return url;
	}
	
	function get_video_icon() {
		return 'data:image/jpeg;base64,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';
	}
	
	function get_audio_icon() {
		return 'data:image/jpeg;base64,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';
	}
	
	function get_zip_icon() {
		return 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAwICAgJCAwJCQwRCwoLERUPDAwPFRgTExUTExgXEhQUFBQSFxcbHB4cGxckJCcnJCQ1MzMzNTs7Ozs7Ozs7OzsBDQsLDQ4NEA4OEBQODw4UFBARERAUHRQUFRQUHSUaFxcXFxolICMeHh4jICgoJSUoKDIyMDIyOzs7Ozs7Ozs7O//CABEIAJYBLAMBIgACEQEDEQH/xAAbAAEAAgMBAQAAAAAAAAAAAAAABAYCAwUHAf/aAAgBAQAAAAD1UAAAAAAAAQY/0+ztwAAAAI1fqv3LZv22jCP0+gAAABhXILP79g170jLzKz3oAAAFc5m3Z9+Yx9mcf7QvVLAAAACvRMcNfzZnnbKhOq/pH0AAAHB4/Sh160cvpbbN55uh+kgAAAcDj/YWvbq78iweeSY3oX0AAAFfgxNeCROm92nya56T9AAABXeT1o3K6vMk/bp53O5vo/0AAAFbgyebz+xzerLsvn++N6D9AAABWuRHxxxldS00nq7aFarkAAACFjDgaX3bFq/oOvyr0S4AAAAI/Eou3KJ1OxXp0P0uSAAAAa9bGp2Nyc63euL0eyAAAA+YavOdf30mq0/qw7pZgAAADHTVO/Cyga+3wbZJAAAAAAAAAAAAAAP/xAAaAQEAAwEBAQAAAAAAAAAAAAAAAwQFAgEG/9oACAECEAAAAAHp4AAJ5+uoq8QAZGbW72foLOdyAGDU941t6xnAAw69L3a3rGeADFyZ2vuWKMYAc+Wp5Ous2MAD1fgu16sYADvyTjnwAAAH/8QAGgEBAQEBAQEBAAAAAAAAAAAAAAQDAgEFBv/aAAgBAxAAAAAAAAB7xDj5XRoAFN/Tj8/lZ0AFFu6b4+VQAKbd2HxMqgAVX69QfGxr7AD3qaWXTS7QAAj1grp6AAOedAAAAH//xAAuEAACAwACAQMCBQIHAAAAAAADBAECBQAGEhEUQBUhEBMWMUEHIyIlJjM0UGD/2gAIAQEAAQgA/wC00trMy5HD0doxpj1j9T43P1Pjc/U+NynZMm8+NFWl2xQZf5zzJQDiACylwFNpaz3bCGbvQNNtiY9Z+tn5TaP/ACLcj19IQ13E9H3AZ3z/AG8CdkMKPUn6zX9fSVNgTBxrk+SQohUm5ewbHX2g1AUO3hjFAhRp4Uz6xGpjfx9Ux+Rp5E/s6mjtiPbGC3N71rOB/wAWnN20UUtaSufmsWmOvzdjczTE+Q65uMELC9814l/zDUSerH2gGjHPHSjkzqfxP1ef2ZtozNwm6LSKaD0R2zqMsn+q5ifax4ngHT3+2Kt597L4wNLYcsNLCzIWIv4/ImJih7QFpJpMJQSWY5ZiY/aWr89wS32qoUtG5UOzXyYJzp9PHQb/AA2oa3jBHwgRiRkbWeupRy5+uR+33+RP+0xzqM+n5vNF8alK3I9opip5hjt5xuVoVY9C+BBrT/qnR5MeRy86xTx0Gfw7rjBC4Kyg+p4ls6fLqjE9QKwLS+TM/wBpjiZszArENE7Ti29eX2Os2tNptrdZ4DsWIH0geO7R7ddaoGPI5uYFfHQP+GtrEw3bUPOz2oqFjq9Jljsfvo1Yj0j0j5Hl40PPLZKZFRORfOSieWQSjkpo89ojETPERDDaJ4uUdGC1viR/mBfw1/ebJhSYrOWunNXFwJaJZP1uPX0+/wAi8/22OdUGMkEqQ4U6enq0EYI8r02cS7NVriXV/Mryl7W7O/EsU8rl50SxPqD9Lc7lgY5dGlkg9axh580F10xunFZhr9/v8kk/4GOdeUMgKSuPRnN0ihjJDJE1knW8i1vOyZVgVpSybAmOxvlDEeVzc6fmXB7l+/NxrQxTxW3ue6FzrHF0KWNuz9daPT+PkW+8nrLeGFsnmxPV8/k9Xz+T1fP5PV8/mfkrIEuQKIbMNXBUQ6CHUQ+39oKwScjK67j50JDg3YRATSvddTaNa9q2xXr23EBK/IvjZpL2Jb6Flc0c9BQdJBWR2p5zLudE+k+8zue7z+LbayVrXBr9pfdVsqnb8lO1az1w4yKUmnbb1jPJxZmlDWies55RtpMl+U6pDYfCLN6kUMm6VbsEMk9KL7/8k+sAHJC13I9fSVxajNYmmR1JVm83Z3uvNde1bMcPnLXzpZL0PCy9Rk7gFs9VW03F8soAm9IL9PR57FHl0M+aWi+hiKjzJ1VMmEdHLUf5uaFsjLsVVPBf2AjY3di9kFrCjCZVQaJGfjPkcEahiPbz+s1C3Vtguvm3Mb5drenCFiKW5q9ygmOPOXTro+xCOyhbU2FBNJ3H+RWY7y1Qa8xGUxSrQbS6BorbZLrduohhlzRf09EUfXokvy7VieXW8uZv9OlcxozAPalDSo6aKTDa8i4CjIRxSdPArqx4mzuljSLUwK57Q4mYJ0UZds2vbOThNWoY/wDL/wD/xABEEAACAQIEAQYHDQYHAAAAAAABAgMAEQQSITFBBRMiQFGSFDJCU2GR0hBScYGUoaKxsrPR0/AjYoKDwcMkM1BgcnTC/9oACAEBAAk/AP8AVJxE0t+bQKzsbbnLGrG3pqSUj/r4j8qpJfk+I/KqSX5PiPyqkl+T4j8qmmY9gw2I/Kp86XKnQggjQqytYgjsPX4+dxEpywoTlW9ibu3BQBrU3h2OOrBASq22UAbAcBUDhFNhoRUUnqNRSeo1FJ3TQZH7DcUQ4mIGIhY5VkA0DXOgcdvxGuTZ5AdijwMPmlrkzEKO0tCP7tYSQHbWWAf3aibDzTKzwhijK4W2bK0TMLi+3WnWNBuzEAes1NPKUbMPArgnQqVL6LYg9tYXHLGNMoC+1WCxt/gT2qweN9Se1WDxvqT2qwmN9Se1UUq4/BAO8cmUZ1N7qpUnpVowNiDoQRwIrsrgKAsCQNKYnmopgo4apbrOIiw8KySRqFQlyEdkuWN97cBUscz++k5xz9JjRw/cf2qOH7r+1Rw/df2qbDj+F/xqWEfwN+NTrkyBjzS5SbkixbcbcKFhkX6zS2xF/wDEwp5Y84o9929vw7wYmOM9FHeJlBa2wJ3rDYkoV/zDGQvrqKRkVv2kiqSqA7ZrfBULKMOjBpZBYsWFtL9Z3E2II+KWSsRiDOyKZY0VSQSNd0qTF9xPy6kxXcT2KkxPcT2KfEljsMib9ypzNOsXOSrZQEuwCr0QNbV5pftPXFF+17kYhwkDmSN8pkd91BI6Krffc1ihFCBYhjHGLfCcxqfm3SVcwgeRhKw8mTMcrDXs6153EfeyV5tPqpsodwmY6gX3J9Vcoxt2KebJ+ZqVZcOWszKLMB2ixNHMrEEGvNr9qvNJ9p64xj7Xuco4hHxMh5zBCW6ots10QdJQT8VQTYrEMvjPzja27WstRjwXFSpzbRurmHdSzqOGovY9a87iPvZKxOeeWNM0aLfLoN6dyP8AjUZJO5yVGe5RcKpuFC0MqyRggHe2avNp9p64xj7XucmYp5XcgYlWCxzMSWzB1BJv6dq5LhgQLcNPIXbuinBfDypzkSoApVgcoHd60L2mxBt8EslQYErOoYF9+kL2vkNQYD9fy6gwP6/l1h8F+v5dYfBG3683WFhwskyFwsXjFAQAW6K730pgpaNLX08p64xf+vcAhwcLGSLKBmO6gu8jAajWwXSuUESMDVGlUD1RAGmWGaGVRHPCjLdhr0nJuw7a349Z87iPvJKUOqxplDagaVEl2IVRYak7CsIrL2q0ZHzsKjEUjnKpZVy37LqTUSbjgKJIWNQo7Bm2FeaX63piyoi5FJuBcm9vckkbGTSk4rDROzqote5jW+S5rk0NOy25yRQNbe+lIpEnwmLkjLiFmLQgXUtYplO+uvWvO4j7ySisCTRpzedgCdKnSytmUrJlII0vcGuVn5vghlBFqxis29y4rFRuE8osL2FNnjZBZhx6VebT63rQYk5I1/dQm7ev3OTeeDsVixBlOSQklyxVbdI7m9R4LBIFuNGkf59KmaaTDSoWBtlYMGsLfw9a2M049cr1iMRIwAAuy2AGgAGSpJu8vs1JN3l9mpJu8PZp5u8PZouzOuU5yDoDfgBWhkVBfsW75j8QoZUQBVHYBT9G+XETqfGI3RSPJHlHjt21hoJZLXZzGpNz6bVDEpA06Ckeoio8OLnW2Hi1+jSLFDLFL4VzahFYhcy3VbC4I36zD0nYsxDMLljcmwbtqD6T+1XJ7YuWQkCNJCpsAWJuzeiuQ3QDcNPY/XXJQuOHhI/GuSh8pH41ySPlI/GuTRGzCzHn1JsOHSvUHgpk6Mk3OKzZeKpk2v20RnO5/oKYEWHGiPFNanMRYb10WMch5viAy8etyNDKpzRTJ4yNtf0+kHeubGJRdGJyh14Oh4g/NUN1zGxzCoPpCobINzespPZeoljVvLY2FLz6g/tJW2J96g/rRlfkvEyEYZIJSrC4zZCN9K5Mx8ihbm84J9WeomR8NIqssrZiim5uB2m1L0iLZiSTb4+uRrJl2zAG1YePuisPF3F/CsNEykHMCim49VYXDRSBRNHEMPCUAtmVTdMx036VYSOPwmJJSmUHKWAJF7cKjUyXWOBCOgGY7kLbQb1jJXKNzkMKCNFUkW4R329NSyNFaxBEe3cqSfDtjGXnSDEwJW+XotGe2iGmwsphd1GUP0VdWtrbRtfTU5wnJ2DZ4+gqEs0ejFmkVt2vtwq3OwTPAzgWD5LENb0hh13sP1UpeZ0WPKurGwAsAKHKkBRACkU+RR6FXnBYVLykyEuxXGyM8VwpykdJluKYajtphc6b0w8YbVh8aEZ8yeDvkuMoF8uYG9LIsxaRSZr85dmJOe+t6Uo8kzuQdDqF366dKlztKxMZdbtGh8hdfnoEqosPioMjAh0fLcBh2jiDtWEjcgatZqwqoP3SwqGMSL4rOWa3rFNmdjmY9poZpZyGCOLqr2sWHw2o5jcszdpP+2P/xAA7EQACAQIDBAMMCgMAAAAAAAABAgMAEQQSIRMxQVEFMPAGFBUiIzJhc4GRkuEgNEJxcoKhwcLRQFJi/9oACAECAQE/APpgE7gaytyPurK3I+6iLaHrwygjMbDtwqHEwRkkgufSK8Iw/wCn6fKkx+HZgpXLm01FY2JAokAF91YbCrKuY6VPGEaw5fvbrOncVNh4I2hfIWkykjlY06dJpvnj7flo4zHA22wP3D5V35jcpO1830fKujHeREdyWY8TUMm0AjkF0X7V9RUUoRCsYtY6E7vaady7XPWd0v1eH1v8TU0rvJohdARcai9uF6WNCxJgIBFrAWt6aaIpFJoRpx+8V0ULQR9uJrB5tpdT+XnTltm+0sUvr41yPd1vdGL4eH1v8TU0WHQ5UVs2gADsdffRSbOQFdbC9r3NudKpZHJJ0HE10Wx2SLbtesJbaa6HgbXtWgVshLNfzTax9nW90A8hF6z9jTOjvnMjKb3GVd360kyjUSN8NM8eVgpJLei1dFx+RVuA/uoQ0a7Rluh0tx9lRoZY2KBUzHQ8RUsZjaxN+1usZFbRlDfiF6EEN9Y0+EVBgcO4OZUW3/O+vBmF5J8IpcDAltbKOA0FSPHN5FTltuqJlgiPHKd+n9mppM7ZuuuedZjzNPh2KAMwRgxt+GpIzCAwbxjrcVh41aG7fa1Nd63OZCp3eLyqYASOBuBPXvI7+cb0HNsuhG/WhiJALA2HKlkdSSpsW0NE3Nz/AJP/xAAxEQACAQIEAwcDAwUAAAAAAAABAhEAAwQSIUETMTIFIjBRYXGBI0BCFJGSM0NSofD/2gAIAQMBAT8A+5CO3QpY1dwWKuCOjTbWj2Jij/cb/vmr3YuNS2zq5OUTFdl4m6TwH1y697mPSsVizZMBQxqzc4iBiInxMKFzEkAxHMTQuMdAIjSMsUAx2H7VEbL+1Ygj9M+gExyEb1csqjteXRyIjSGPzTWVds10FiwAhdvikQIoUch4mG5n4rIWcsFJBq2hAgqW9TWQiSRtH+6xB+m49qv9B0+YnL61b6hkbXLudCPPxcLzb4oOVYqSTSNmEiT7VMmPSaxNscF3B8pFX/6bbiO8IkkeQFLErmGQZdD0/Hi4Pqb4oW9cxbX0FKoHJjWRRqCeUVjb2UG0Pygn2q5ctu/BBlwJ9B7099bLw8tlXUAb+Yk1bcOoYcj5+IrMvSSPYxXFuf5t/I1fx1+2wC520nqNN2vix+D/AMjVztXFvIFrvH8tWNYXDXrX137xI1Xf3p7PHdpMZgIjWB68qtW+GgTnH2C4g5mIR7iaDur+W9Lc4pKshCkaq4rEX3S5CQMpy8hyO1NiSF7yONg0aE+lW82Rc3VlE++/2GUTm3iKNi2ZJUGdZqNvuv/Z';
	}
	
	function get_pdf_icon() {
		return 'data:image/jpeg;base64,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';
	}
	
	function get_other_icon() {
		return 'data:image/jpeg;base64,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';
	}
});