<?php get_header(); ?>

    <main>
        <h1><?php the_archive_title(); ?></h1>

        <?php if ( have_posts() ) : ?>

            <?php while ( have_posts() ) : the_post(); ?>

                <article>
                    <h2>
                        <a href="<?php the_permalink(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h2>

                    <?php the_excerpt(); ?>
                </article>

            <?php endwhile; ?>

        <?php else : ?>

            <p>No messages found you fat fuck...</p>

        <?php endif; ?>
    </main>

<?php get_footer(); ?>