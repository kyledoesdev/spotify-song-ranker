<?php

arch('System: Uses PHP preset')->preset()->php();
arch('System: Uses no debug methods')->expect(['dd', 'dump', 'die', 'ray'])->not->toBeUsed();
