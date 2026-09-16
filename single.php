<?php get_header(); ?>

<main>

    <?php if (have_posts()) : ?>

        <?php while (have_posts()) : the_post(); ?>

            <article>               
                <h2><?php the_title(); ?></h2>

                <p><?php the_content(); ?></p>
            </article>

        <?php endwhile; ?>

    <?php else : ?>

        <p>Geen posts gevonden.</p>

    <?php endif; ?>

</main>

<?php get_footer(); ?>