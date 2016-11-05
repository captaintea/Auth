const elixir = require('laravel-elixir');

elixir(function (mix) {
	mix.sass('app.scss').scripts(
        [''],
        './public/js/app.js'
    );
	if (process.env.NODE_ENV === 'development') {
		mix.browserSync({
			proxy: '192.168.10.45'
		});
	} else {
		mix.version([
			'css/app.css'
			, 'js/app.js'
		]);
	}
});
