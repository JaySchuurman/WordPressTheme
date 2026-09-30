<?php
/**
 * The template for displaying 404 (page not found) errors.
 *
 * Place this file in the root of your (child) theme folder.
 */

get_header(); ?>

<main id="primary" class="site-main error-404 not-found">
	<section class="error-404-content" style="max-width: 720px; margin: 4rem auto; padding: 0 1rem; text-align: center;">

		<header class="page-header">
			<h1 class="page-title"><?php esc_html_e( '404 – Page not found', 'textdomain' ); ?></h1>
		</header>

		<div class="page-content">
			<p><?php esc_html_e( "Sorry, we couldn't find the page you were looking for. It may have been moved or deleted, or the link may be incorrect.", 'textdomain' ); ?></p>

			<?php get_search_form(); ?>

			<p style="margin-top: 2rem;">
				<a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php esc_html_e( 'Back to homepage', 'textdomain' ); ?>
				</a>
			</p>

			<?php
			// Show the 5 most recent posts as suggestions.
			$recent = new WP_Query( array(
				'posts_per_page'      => 5,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			) );

			if ( $recent->have_posts() ) : ?>
				<h2><?php esc_html_e( 'Recent posts', 'textdomain' ); ?></h2>
				<ul style="list-style: none; padding: 0;">
					<?php while ( $recent->have_posts() ) : $recent->the_post(); ?>
						<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
					<?php endwhile; wp_reset_postdata(); ?>
				</ul>
			<?php endif; ?>
		</div>

	</section>
</main>

<?php get_footer();