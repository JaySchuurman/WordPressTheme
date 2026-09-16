<?php get_header(); ?>

<main>

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

</main>

<?php get_footer(); ?>