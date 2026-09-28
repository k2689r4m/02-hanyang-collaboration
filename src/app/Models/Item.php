<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\jsonCast;
use App\Casts\nullable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'id','title', 'cardId', 'content', 'images', 'files', 'activationImage', 'deadLine', 'activationDeadline','activationFile',
        'label', 'activationLabel', 'checks', 'activationCheck', 'brain', 'activationParty', 'userId', 'party', 'type', 'basicApplyId', 'consultingApplyId'
    ];

    protected $casts = [
        'images' => 'array',
        'files' => 'array',
        'checks' => jsonCast::class,
        'brain' => 'array',
        'party' => jsonCast::class,
        'activationImage' => 'boolean',
        'activationDeadline' => 'boolean',
        'activationLabel' => 'boolean',
        'activationCheck' => 'boolean',
        'activationParty' => 'boolean',
    ];

    public function card ()
    {
        return $this->belongsTo(Card::class, 'cardId', 'id')->first();
    }

    public function withCard ()
    {
        return $this->belongsTo(Card::class, 'cardId', 'id')->select('id', 'teamId')->with(['withTeam' => function ($query) {
            $query->select('id', 'name');
        }]);
    }

    public function user ()
    {
        return $this->belongsTo(user::class, 'userId', 'id');
    }

    public function itemFileList () {
        return $this->hasMany(ItemFileList::class, 'itemId', 'id')->where('type', 'image')->get();
    }

    public function withImages () {
        return $this->hasMany(ItemFileList::class, 'itemId', 'id')->where('type', 'image');
    }

    public function itemFiles () {
        return $this->hasMany(ItemFileList::class, 'itemId', 'id')->where('type', 'file')->get();
    }

    public function withFiles () {
        return $this->hasMany(ItemFileList::class, 'itemId', 'id')->where('type', 'file');
    }

    public function withProblemAnalysis () {
        return $this->hasOne(ProblemAnalysis::class, 'itemId', 'id');
    }

    public function problemAnalysis () {
        return $this->hasOne(ProblemAnalysis::class, 'itemId', 'id')->select('id', 'itemId');
    }

    public function withReflectionLog () {
        return $this->hasOne(ReflectionLog::class, 'itemId', 'id');
    }

    public function reflectionLog () {
        return $this->hasOne(ReflectionLog::class, 'itemId', 'id')->select('id', 'itemId');
    }

    public function withClassApply () {
        return $this->hasOne(ClassApply::class, 'itemId', 'id');
    }

    public function withBasicApply () {
        return $this->hasOne(BasicApply::class, 'id', 'basicApplyId')->with('basic');
    }

    public function withConsultingApply () {
        return $this->hasOne(ConsultingApply::class, 'id', 'consultingApplyId')->with('consulting')->with('classApply');
    }

    public function withBrains () {
        return $this->hasMany(Brain::class, 'itemId', 'id')->with('withUser');
    }

    public function createBrains () {
        return $this->hasMany(Brain::class, 'itemId', 'id');
    }

    public function classApply () {
        return $this->hasOne(ClassApply::class, 'itemId', 'id');
    }

    public function classApplyObject () {
        return $this->hasOne(ClassApply::class, 'itemId', 'id')->with('classObject')->orderBy('id', 'desc');
    }

    public function withTeamActivity () {
        return $this->hasOne(TeamActivity::class, 'itemId', 'id');
    }

    public function teamActivity () {
        return $this->hasOne(TeamActivity::class, 'itemId', 'id')->select('id', 'itemId');
    }

    public function withOperationResult () {
        return $this->hasOne(operation::class, 'itemId', 'id');
    }

    public function withComments () {
        return $this->hasMany(Comment::class, 'itemId', 'id');
    }

    public function comments () {
        return $this->hasMany(Comment::class, 'itemId', 'id')
            ->with(['withUser' => function ($query) {
            $query->select('id', 'name');
        }])->get();
    }

    public function comments2 () {
        return $this->hasMany(Comment::class, 'itemId', 'id')
            ->with(['withUser' => function ($query) {
            $query->select('id', 'name', 'authority')->where('authority', 1);
        }]);
    }

    public function commentsOrderByDesc () {
        return $this->hasMany(Comment::class, 'itemId', 'id')
            ->with(['withUser' => function ($query) {
            $query->select('id', 'name');
        }])->orderBy('id', 'desc')->get();
    }

    public function getComments() {
        return $this->hasMany(Comment::class, 'itemId', 'id')->get();
    }

    public function classListCount(){
        return $this->hasMany(ClassList::class, 'id', 'classObjectId')->get();
    }

    public function problemCount($list){
        $itemsNum = [$list];
        return $this->hasMany(ProblemAnalysys::class,'id',[$list])->count();
    }

    public function teamCount(){
        return $this->hasMany(teams::class, 'classObjectId', 'classObjectId')->count();
    }






    //권한 체크
    public function isPermitted ($userId, $applyId = null) {
        //description
        //Flow(이하 Card로 명칭), Stack(이하 Item으로 명칭)
        //
        //1. $userId 삭제 예정 (Auth facade로 대체 예정)
        //
        //2. Card 데이터베이스는 하위 3가지 중 한가지로 무조건 형식이 고정임
        //2.1. 마이페이지의 카드 일 때: [myPageId: id, classObjectId: null, teamId: null]
        //2.2. 클레스의 카드 일 때:    [myPageId: null, classObjectId: id, teamId: null]
        //2.3. 팀의 카드 일 때:       [myPageId: null, classObjectId: id, teamId: id]
        //
        //3. Item의 타입은 하위 항목에 따라 정의 됨
        //0: 일반 아이템
        //1: 문제분석
        //2: 성찰
        //4: 수업개설
        //5: 팀활동
        //3.1. Item의 1,2는 MyClass의 onTeamAccess 속성에 따라 다른 팀이 접근 가능하므로 특수한 경우로 권한 체크를 따로 해야함
        $SPECIAL_ITEM_TYPE = [1, 2, 5];
        $SPECIAL_CARD_TYPE = [7, 9, 10];

        $card = $this->card();

        //desc 2.3 팀의 카드일 경우
        if (!is_null($card->teamId)) {
            //desc 3.1 특수한 경우
            if (in_array($this->type, $SPECIAL_ITEM_TYPE)) {
                if ($card->myClass()->onTeamAccess) {
                    if (in_array($card->type, $SPECIAL_CARD_TYPE)) {
                        //팀 엑세스가 켜진 경우 클래스의 멤버인지 검사 후 결과 반환
                        if ($card->myClass()->userId == Auth::id()) {
                            return true;
                        }
                        if (Auth::user()->authority == 5) {
                            $expert = Expert::where('classObjectId', $card->myClass()->classObjectId)->where('userId', Auth::id())->first();
                            if ($expert) {
                                return true;
                            }
                            else {
                                return false;
                            }
                        }
                        if (Auth::user()->authority == 6) {
                            $manager = ClassManager::where('classObjectId', $card->myClass()->classObjectId)->where('userId', Auth::id())->first();
                            if ($manager) {
                                return true;
                            }
                            else {
                                return false;
                            }
                        }

                        return $card->myClass()->isMember(Auth::id());
                    }
                }
            }
        }

        if (Auth::user()->authority == 4) {
            if (!is_null($this->card()->myPageId) && !is_null($applyId)) {
                $myPage = MyPage::find($this->card()->myPageId);
                if (!$myPage) {
                    return false;
                }

                $userId = $myPage->userId;

                $user = User::find($userId);
                if (!$user) {
                    return false;
                }
                else if ($user->authority != 2) {
                    return false;
                }

                $consultingApply = ConsultingApply::find($applyId);
                if (!$consultingApply) {
                    return false;
                }

                if ($consultingApply->userId != $myPage->userId) {
                    return false;
                }

                $consulting = $consultingApply->consulting()->first();
                if (!$consulting) {
                    return false;
                }

                if ($consulting->consultantId != Auth::id()) {
                    return false;
                }

                if (strtotime($consulting->startDateTime) > strtotime('now')) {
                    return false;
                }

                if (strtotime($consulting->endDateTime) < strtotime('now')) {
                    return false;
                }

                return true;
            }
            else {
                return false;
            }
        }

        // Models\card.php\isPermitted() 참고
        // 카드에 접근 가능한 사용자인지 확인
        return $this->card()->isPermitted(Auth::id());
    }

    public function remove() {
        if (Auth::id() != $this->userId && Auth::user()->authority !=  3) {
            return false;
        }

        $card = $this->card();

        $files = [];
        foreach ($this->getComments() as $comment) {
            $files[] = 'comment/'.$comment->filePathName;
        }
        foreach ($this->itemFileList() as $image) {
            $files[] = 'images/'.$image->pathName;
        }
        foreach ($this->itemFiles() as $file) {
            $files[] = 'files/'.$file->pathName;
        }
        Storage::disk('local')->delete($files);
        $itemsNum = $card->itemsNum;
        $key = array_search($this->id, $itemsNum);
        array_splice($itemsNum, $key, 1);
        $card->itemsNum = $itemsNum;
        $card->save();
        $this->delete();

        return true;
    }

    public function removeWithCard() {
        if (Auth::id() != $this->userId && Auth::user()->authority !=  3) {
            return false;
        }

        $files = [];
        foreach ($this->getComments() as $comment) {
            $files[] = 'comment/'.$comment->filePathName;
        }
        foreach ($this->itemFileList() as $image) {
            $files[] = 'images/'.$image->pathName;
        }
        foreach ($this->itemFiles() as $file) {
            $files[] = 'files/'.$file->pathName;
        }
        Storage::disk('local')->delete($files);
        $this->delete();

        return true;
    }

    public function isPermitted2 ($userId, $applyId = null) {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }
        $SPECIAL_ITEM_TYPE = [1, 2, 5];

        //desc 2.3 팀의 카드일 경우
        if (!is_null($this->card()->teamId)) {
            //desc 3.1 특수한 경우
            if (in_array($this->type, $SPECIAL_ITEM_TYPE)) {
                if ($this->card()->myClass()->onTeamAccess) {
                    //팀 엑세스가 켜진 경우 클래스의 멤버인지 검사 후 결과 반환
                    if ($this->card()->myClass()->userId == $userId) {
                        return true;
                    }
                    return $this->card()->myClass()->isMember($userId);
                }
            }
        }

        if ($user->authority == 4) {
            if (!is_null($this->card()->myPageId) && !is_null($applyId)) {
                $myPage = MyPage::find($this->card()->myPageId);
                if (!$myPage) {
                    return false;
                }

                $userId = $myPage->userId;

                $user = User::find($userId);
                if (!$user) {
                    return false;
                }
                else if ($user->authority != 2) {
                    return false;
                }

                $consultingApply = ConsultingApply::find($applyId);
                if (!$consultingApply) {
                    return false;
                }

                if ($consultingApply->userId != $myPage->userId) {
                    return false;
                }

                $consulting = $consultingApply->consulting()->first();
                if (!$consulting) {
                    return false;
                }

                if ($consulting->consultantId != $userId) {
                    return false;
                }

                if (strtotime($consulting->startDateTime) > strtotime('now')) {
                    return false;
                }

                if (strtotime($consulting->endDateTime) < strtotime('now')) {
                    return false;
                }

                return true;
            }
            else {
                return false;
            }
        }

        // Models\card.php\isPermitted() 참고
        // 카드에 접근 가능한 사용자인지 확인
        return $this->card()->isPermitted2($userId);
    }
    
    public function problems () {
        $test = $this->hasMany(Card::class, 'classObjectId', 'id')->with('withItemsWithProblems')->get();

        $sum = 0;
        foreach ($test as $_test) {
            if ($_test->withItemsWithProblems->count() > 0) {
                foreach ($_test->withItemsWithProblems as $__test) {
                    if ($__test->withProblemAnalysis) {
                        $sum += $__test->withProblemAnalysis->count();
                    }
                }
            }
        }
        return $sum;
    }
}
