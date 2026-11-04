<?php

function dg_album_post_type() {
	return apply_filters( 'dg_album_post_type', 'post' );
}

function dg_get_tracks_data() {
	$cached = get_transient( 'dg_tracks_data' );
	if ( false !== $cached ) {
		return $cached;
	}

	$tracks = array();
	$query  = new WP_Query( array(
		'post_type'      => dg_album_post_type(),
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'no_found_rows'  => true,
	) );

	foreach ( $query->posts as $post ) {
		$raw = $post->post_content; 
		if ( ! preg_match_all( '/<li[^>]*>(.*?)<\/li>/is', $raw, $m ) ) {
			continue;
		}

		$img   = get_the_post_thumbnail_url( $post->ID, 'large' );
		$album = get_the_title( $post );
		$url   = get_permalink( $post );

		foreach ( $m[1] as $li ) {
			$name = trim( html_entity_decode( wp_strip_all_tags( $li ), ENT_QUOTES, 'UTF-8' ) );
			if ( '' === $name ) {
				continue;
			}
			$tracks[] = array(
				'track' => $name,
				'album' => $album,
				'url'   => $url,
				'img'   => $img ? $img : '',
			);
		}
	}

	set_transient( 'dg_tracks_data', $tracks, 12 * HOUR_IN_SECONDS );
	return $tracks;
}

// Refresh the cached track list whenever posts change.
foreach ( array( 'save_post', 'deleted_post', 'trashed_post' ) as $hook ) {
	add_action( $hook, function () {
		delete_transient( 'dg_tracks_data' );
	} );
}

/** Loads the inline JS once, in the footer, only on pages that use a shortcode. */
function dg_enqueue_extras() {
	static $done = false;
	if ( $done ) {
		return;
	}
	$done = true;
	wp_register_script( 'dg-extras', false, array(), '1.0', true );
	wp_enqueue_script( 'dg-extras' );
	wp_add_inline_script( 'dg-extras', dg_extras_js() );
}

add_shortcode( 'dg_random_track', function () {
	$data = dg_get_tracks_data();
	if ( empty( $data ) ) {
		return '';
	}
	dg_enqueue_extras();

	ob_start(); ?>
	<div class="dg-random" data-tracks="<?php echo esc_attr( wp_json_encode( $data ) ); ?>">
		<button type="button" class="dg-btn dg-random-btn">Random track</button>
		<div class="dg-random-result" aria-live="polite" hidden>
			<img class="dg-random-cover" alt="" hidden>
			<div class="dg-random-text">
				<p class="dg-random-track"></p>
				<a class="dg-random-album" href="#"></a>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
} );

add_shortcode( 'dg_quiz', function () {
	dg_enqueue_extras();
	return '<div class="dg-quiz"></div>';
} );

/** The JavaScript for both features. */
function dg_extras_js() {
	return <<<'JS'
(function () {
	function el(tag, cls, text) {
		var n = document.createElement(tag);
		if (cls) n.className = cls;
		if (text !== undefined) n.textContent = text;
		return n;
	}

	/* ===== Random track ===== */
	document.querySelectorAll(".dg-random").forEach(function (box) {
		var tracks = [];
		try { tracks = JSON.parse(box.getAttribute("data-tracks")); } catch (e) {}

		var btn = box.querySelector(".dg-random-btn");
		var res = box.querySelector(".dg-random-result");
		var img = box.querySelector(".dg-random-cover");
		var name = box.querySelector(".dg-random-track");
		var alb = box.querySelector(".dg-random-album");
		var last = -1;

		btn.addEventListener("click", function () {
			if (!tracks.length) return;
			var i;
			do { i = Math.floor(Math.random() * tracks.length); }
			while (tracks.length > 1 && i === last);
			last = i;

			var t = tracks[i];
			name.textContent = t.track;
			alb.textContent = t.album;
			alb.href = t.url;
			if (t.img) {
				img.src = t.img;
				img.alt = t.album + " cover";
				img.hidden = false;
			} else {
				img.hidden = true;
			}
			res.hidden = false;
			btn.textContent = "Again";
		});
	});

	/* ===== Quiz ===== */
	var ALBUMS = {
		exm:  { name: "Exmilitary", text: "Raw, sample-heavy and furious. You like things rough around the edges and loud enough to hurt." },
		ms:   { name: "The Money Store", text: "Glitchy, hyper-kinetic and surprisingly sharp. You want chaos with a razor edge on it." },
		nldw: { name: "No Love Deep Web", text: "Stripped down, blunt and confrontational. No frills, just impact." },
		gp:   { name: "Government Plates", text: "Cold, mechanical and experimental. You care more about strange textures than hooks." },
		tptb: { name: "The Powers That B", text: "Sprawling and unpredictable, with two very different halves. You would rather get lost than be told where to go." },
		bp:   { name: "Bottomless Pit", text: "Heavy, guitar-driven and relentless. You want a wall of noise and you want it now." },
		yots: { name: "Year of the Snitch", text: "Strange, theatrical and darkly funny. You love a weird left turn." }
	};

	var QUESTIONS = [
		{ q: "Pick your ideal Friday night.", a: [
			["A sweaty basement show", { exm: 2, bp: 2 }],
			["A club with the bass turned way too high", { ms: 3, tptb: 1 }],
			["Alone at 3am with headphones and a strange playlist", { gp: 2, yots: 2 }],
			["Driving nowhere with the windows down", { nldw: 2, tptb: 1 }]
		] },
		{ q: "What should the music sound like?", a: [
			["Distorted guitars and pounding drums", { bp: 3, tptb: 1 }],
			["Glitchy electronics with huge drums", { ms: 3, gp: 1 }],
			["Cold industrial machinery", { gp: 3, nldw: 1 }],
			["A collage of gritty samples", { exm: 3, nldw: 1 }]
		] },
		{ q: "How do you feel about polish?", a: [
			["Rough and raw only", { exm: 2, nldw: 2 }],
			["Polished, but still unhinged", { ms: 3 }],
			["I do not care if it is pretty", { gp: 2, tptb: 2 }],
			["Weird, but somehow catchy", { yots: 3, ms: 1 }]
		] },
		{ q: "Pick a mood.", a: [
			["Furious", { bp: 2, nldw: 2, exm: 1 }],
			["Paranoid", { gp: 2, nldw: 1, yots: 1 }],
			["Manic", { ms: 2, tptb: 2 }],
			["Wry and strange", { yots: 3, tptb: 1 }]
		] },
		{ q: "How long should an album be?", a: [
			["Short and punchy", { nldw: 2, ms: 1, bp: 1 }],
			["Long and sprawling", { tptb: 3, yots: 1 }],
			["A full journey with twists", { yots: 2, tptb: 2 }],
			["One sitting, no skips", { ms: 2, gp: 1, exm: 1 }]
		] },
		{ q: "Your energy is...", a: [
			["Chaotic", { ms: 2, tptb: 2 }],
			["Locked in", { gp: 2, nldw: 2 }],
			["Restless", { exm: 2, bp: 2 }],
			["Unpredictable", { yots: 2, tptb: 2 }]
		] }
	];

	document.querySelectorAll(".dg-quiz").forEach(function (root) {
		var idx = 0;
		var score = {};

		function reset() { idx = 0; score = {}; render(); }

		function render() {
			root.textContent = "";
			if (idx >= QUESTIONS.length) { result(); return; }

			var q = QUESTIONS[idx];
			root.appendChild(el("p", "dg-quiz-progress", "Question " + (idx + 1) + " / " + QUESTIONS.length));
			root.appendChild(el("h3", "dg-quiz-question", q.q));

			var list = el("div", "dg-quiz-answers");
			q.a.forEach(function (ans) {
				var b = el("button", "dg-btn dg-quiz-answer", ans[0]);
				b.type = "button";
				b.addEventListener("click", function () {
					for (var k in ans[1]) score[k] = (score[k] || 0) + ans[1][k];
					idx++;
					render();
				});
				list.appendChild(b);
			});
			root.appendChild(list);
		}

		function result() {
			var best = null;
			for (var k in ALBUMS) {
				if (best === null || (score[k] || 0) > (score[best] || 0)) best = k;
			}
			var a = ALBUMS[best];

			root.appendChild(el("p", "dg-quiz-progress", "You are"));
			root.appendChild(el("h3", "dg-quiz-result-title", a.name));
			root.appendChild(el("p", "dg-quiz-result-text", a.text));

			var row = el("div", "dg-quiz-actions");
			var share = el("button", "dg-btn", "Share result");
			share.type = "button";
			share.addEventListener("click", function () {
				var msg = "I got " + a.name + " on the Which Death Grips album are you quiz.";
				var url = location.href.split("#")[0];
				if (navigator.share) {
					navigator.share({ title: document.title, text: msg, url: url }).catch(function () {});
				} else if (navigator.clipboard) {
					navigator.clipboard.writeText(msg + " " + url).then(function () {
						share.textContent = "Copied";
					});
				}
			});

			var again = el("button", "dg-btn dg-btn-ghost", "Retake");
			again.type = "button";
			again.addEventListener("click", reset);

			row.appendChild(share);
			row.appendChild(again);
			root.appendChild(row);
		}

		render();
	});
})();
JS;
}