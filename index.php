<?php get_header(); ?>

<main class="content-wrapper">

    <section class="posts-list">
        <?php echo do_shortcode('[dg_random_track]'); ?>

        <p>
            <a class="dg-btn dg-btn-ghost" href="<?php echo esc_url(home_url('/quiz/')); ?>">
                Which album are you?
            </a>
        </p>
        <?php if (have_posts()): ?>

            <?php while (have_posts()):
                the_post(); ?>

                <article>
                    <h2>
                        <a href="<?php the_permalink(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h2>
                </article>

            <?php endwhile; ?>

        <?php else: ?>

            <p>Geen posts gevonden.</p>

        <?php endif; ?>
    </section>

    <aside class="content-image">
        <img src="<?php echo esc_url(get_template_directory_uri() . '/images/DG.png'); ?>" alt="">
    </aside>

</main>

<?php get_footer(); ?>