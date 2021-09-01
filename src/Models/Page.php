<?php

namespace LidyaPos\Page\Models;

use LidyaPos\ACL\Models\User;
use LidyaPos\Base\Enums\BaseStatusEnum;
use LidyaPos\Base\Models\BaseModel;
use LidyaPos\Base\Traits\EnumCastable;
use LidyaPos\Revision\RevisionableTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends BaseModel
{
    use RevisionableTrait;
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pages';

    /**
     * @var bool
     */
    protected $revisionEnabled = true;

    /**
     * @var bool
     */
    protected $revisionCleanup = true;

    /**
     * @var int
     */
    protected $historyLimit = 20;

    /**
     * @var array
     */
    protected $dontKeepRevisionOf = ['content'];

    /**
     * The date fields for the model.clear
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'content',
        'image',
        'template',
        'description',
        'is_featured',
        'status',
        'user_id',
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}
