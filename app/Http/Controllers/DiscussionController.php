<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

require_once ('SocietyWrapper.php');
require_once ('DiscussionWrapper.php');
require_once ('PostWrapper.php');

// TODO: add sorting and filtering for newest discussion

class DiscussionController extends Controller
{
    public function show(Request $request)
    {
        $user_id = Auth::id();
        $society_id = $request->input('society_id');
        $discussion_id = $request->input('discussion_id');
        $sorting_method = $request->input('sort');

        $society = \SocietyWrapper::getSocietyFromId($society_id);
        $all_discussions = \DiscussionWrapper::sortSocDiscussion($society_id, 'desc');

        if(is_null($discussion_id))
        {
            $discussion = \DiscussionWrapper::getNewestSocDiscussion($society_id);
            if(!is_null($discussion))
            {
                $discussion_id = $discussion->id;
            }
        }
        else
        {
            $discussion = \DiscussionWrapper::getDiscussionFromId($discussion_id);
        }

        $isInSociety = \SocietyWrapper::isInSociety($user_id, $society_id);

        // Different sorting methods
        if(is_null($sorting_method))
        {
            $posts = \PostWrapper::sortSocPostByCreateTime($discussion_id, 'desc');
        }
        else if($sorting_method=='updateDesc')
        {
            $posts = \PostWrapper::sortDiscPostByUpdateTime($discussion_id, 'desc');
        }
        else if($sorting_method=='filesOnly')
        {
            $posts = \PostWrapper::getPostsWithFiles($discussion_id);
        }
        // TODO: add logic to filter and sort for the newsest discussion
        return view('listDiscussions', ['inSociety'=>$isInSociety, 'all_dis' => $all_discussions, 'discussion' => $discussion,
            'society' => $society, 'posts' => $posts]);
        //return response()->json($posts);
    }

    public function discussionCreation(Request $request) {
        $society_id = $request->input('society_id');
        $society = \SocietyWrapper::getSocietyFromId($society_id);
        return view('discussionCreation', ['society'=>$society]);
    }

    public function create(Request $request)
    {
        $society_id = $request->input('society_id');
        $quarter = $request->input('quarter');
        $year = $request->input('year');
        $discussion = \DiscussionWrapper::createDiscussion($society_id, $quarter, $year);
        //return response()->json($discussion);
        return redirect()->action(
            'DiscussionController@show', ['society_id' => $society_id, 'discussion_id'=>$discussion->id]
        );
    }

}