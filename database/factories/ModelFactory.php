<?php

use App\Models\Comment;
use App\Models\User;
use Faker\Generator;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, function (Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(Comment::class, function (Generator $faker) {
    return [
        'user_id' => $faker->randomNumber(1),
        'post_id' => $faker->randomNumber(1),
        'reddit_id' => $faker->userName,
        'score' => $faker->randomNumber(2),
        'battle_total_wins' => $faker->randomNumber(2),
        'battle_total_losses' => $faker->randomNumber(2),
        'body_md' => $faker->sentence(),
        'is_enabled' => true,
        'created_at' => $faker->dateTime(),
    ];
});

$factory->defineAs(Comment::class, 'imgurDirect', function (Generator $faker) {
    return array_merge($factory->raw(Comment::class), [
        'body_md' => implode(' ', [
            $faker->sentence(),
            'http://i.imgur.com/fpvAapE.jpg',
            $faker->sentence(),
        ]),
    ]);
});

$factory->defineAs(Comment::class, 'imgurNormal', function (Generator $faker) {
    return array_merge($factory->raw(Comment::class), [
        'body_md' => implode(PHP_EOL, [
            "[outfit](http://imgur.com/u3LZfIN)",
            "Polo Hoodie",
            "Vince T-shirt",
            "American Eagle",
            "Nike",
            "[Shoe detail](http://imgur.com/7VzWa9N)",
        ]),
    ]);
});

// @TODO
/* imgur commas
[Shirt by Artistry in Motion + Adidas x Neighborhood Cityrun](http://imgur.com/7IqfH9N,jv8T4vC,RINN1QJ,bxkzfOZ#3)
Also some crappy jorts. Wore this to my final day of classes *ever* as an undergrad. Commencement ceremony on Saturday, couldn't be more hyped.
*/

/* dso direct
[Adidas originals sample/j crew/levis commuter/nike](http://cdn.dressed.so/i/5597317cb87ddl.jpg)
*/

/* dso page
[Really nice spring day, besides all the pollen](http://dressed.so/post/view/5165a3a68f1eb)

Old Navy/Levis 511/ Adidas sambas

Also a crappy pair of knockoff wayfarers.
*/

/* imgur album
[Today](http://imgur.com/a/4847m)
Basics yo
*/

/* imgur wonky
This is from awhile back..

Tried to do [a little experimenting..](http://imgur.com/y9QB34A&amp;viUEq2C)

The jacket is a [burmese traditional formal jacket called "teik-pon"] (http://imgur.com/2ne8PH8)
[Details](http://imgur.com/VRburXD)
[Pocket details.. it's got this little coin pouch kinda thingy](http://imgur.com/MjQnHm9)

It's normally [worn with a white band collar shirt and this thing called longyi which is basically like men's version of sarong..](http://asianlite.com/wp-content/uploads/2014/09/ce6e69bfdcc2231a309f1c0bdb069330.jpg)
*/

/* other .jpg
Two fits today.
[**FIRST, summer**](http://casualism.pl/wp-content/uploads/2015/06/17.jpg)
- H&amp;M linen tee [DETAILS](http://casualism.pl/wp-content/uploads/2015/06/37.jpg)
- Bershka shorts
- Converse

[**SECOND, few weeks ago**](http://casualism.pl/wp-content/uploads/2015/06/24.jpg)
- C&amp;A denim shirt [DETAIL](http://casualism.pl/wp-content/uploads/2015/06/34.jpg)
- Zara tee
- Pull&amp;Bear jeans
- Converse shoes
*/


/* imgur gallery

[First day of my internship last week](http://www.imgur.com/gallery/n3SlXJq)

The colors look weird but:

Shirt: (Orange not red like in the picture) Banana Republic

Tie: (grey almost the same color as the pants) Banana Republic

Pants: Uniqlo

Shoes: Clarks

EDIT: for reference here is the shirt and tie that I took a picture of before I bought them http://www.imgur.com/gallery/SS9X41k
*/

/* imgur not found

Date night tonight, going for a walk along the waterfront.

[With jacket](http://i.imgur.com/BCvPe)

[Wiv out](http://i.imgur.com/pkN57)

[Ignore the missing laces please](http://i.imgur.com/jcZh5)

Levi's commuter trucker jacket, H&amp;M shawl collar cardigan (still not sure I can pull off the cardigan look), Gap lived-in slim fit chinos, Roots chukka boot.
*/


/* no images
Remember guys, upvote this thread!! We need to get this to /r/all, for research purposes!
*/

/* imgur gallery /new

[Vacation on the gulf coast](http://imgur.com/gallery/tnpfo/new)

* Jcrew shirts
* Polo swimsuits
* Chaco sandals

Spring it up!
*/
