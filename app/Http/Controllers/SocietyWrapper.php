<?php

    use App\Society;
    use App\UserSociety;

    // This class wraps up the society table by providing different methods of accessing the table.
    class SocietyWrapper
    {

        static public function joinSociety($user_id, $society_id)
        {
            //$query = DB::table('user_society')
            //    ->where('user_id', $user_id)
            //    ->where('society_id', $society_id)
            //    ->get();
            
            //if(!$query)
            //{
                DB::table('user_society')->insert(
                    ['user_id' => $user_id, 'society_id' => $society_id]
                );  
            //}
        }

        static public function quitSociety($user_id, $society_id)
        {
            $query = DB::table('user_society')
                ->where('user_id', $user_id)
                ->where('society_id', $society_id)
                ->get();
            
            if($query)
            {
                DB::table('user_society')
                    ->where('user_id', $user_id)
                    ->where('society_id', $society_id)
                    ->delete();
            }
        }

        static public function isInSociety($user_id, $society_id)
        {
            $query = DB::table('user_society')
                ->where('user_id', $user_id)
                ->where('society_id', $society_id)
                ->first();

            if($query) {
                return 1;
            }

            return 0;
        }

        static public function createSociety($name, $catagory)
        {
            $society = new Society();
            $society->name = $name;
            $society->catagory = $catagory;
            $society->save();
            return $society;
        }

        static public function deleteSociety($id)
        {
            App\Society::destroy($id);
        }


        /**
        * This function returns a table with all the societies a user is in.
        * @param int $user_id
        * @return table a database table containing the societies the user is in
        */

        static public function getSocietiesForUser($user_id)
        {
            /**
            $societyids = DB::table('user_society')
                            ->where('user_id', $user_id)
                            ->get();

            if($societyids)
            {
                foreach($societyids as $id)
                {
                    $query2 = DB::table('societies')
                        ->where('id', $id)
                        ->pluck('name');
                }
            }
             */
            $user_society = App\UserSociety::where('user_id', $user_id)->get();
            return $user_society;
        }

        static public function getAllSocieties()
        {
            $societies = App\Society::all();
            return $societies;
        }

        static public function getSocietiesFromIds($society_ids) {
            $societies = array();
            foreach ($society_ids as $society_id) {
                //$id = $society_id->society_id;
                //$name = self::getSocietyName($id);
                //$societies[$id] = $name;
                //throw new ErrorException($name);
                $society = DB::table('societies')->where('id', $society_id->society_id)->first();
                array_push($societies, $society);
            }
            return $societies;
        }

        static public function getSocietyFromId($society_id) {
            $society = DB::table('societies')->where('id', $society_id)->first();
            return $society;
        }

        static public function getSocietiesUserNotIn($user_id)
        {
            $all = self::getAllSocieties();
            $societies_in = self::getSocietiesForUser($user_id);
            $society_ids_in = array();
            foreach ($societies_in as $society)
            {
                array_push($society_ids_in, $society->society_id);
            }
            // get index of societies user not in
            $index = array();
            $initial = 0;
            foreach ($all as $society)
            {
                if(in_array($society->id, $society_ids_in))
                {
                    //unset($society, $all);
                    array_push($index, $initial);
                }
                $initial++;
            }
            //remove socities a user is in from $all
            foreach ($index as $i)
            {
                unset($all[$i]);
            }
            // get a new array
            $result = array();
            foreach($all as $key => $value)
            {
                array_push($result, $value);
            }
            return $result;
        }

        // TODO: deprecate this method
        // Fix this method
        static private function getSocietyName($society_id) {
            $society = DB::table('societies')->where('id', $society_id)->first();
            $name = $society->name;
            //foreach ($societies as $society) {
            //    $name = $society->name;
            //}
            return $name;
        }

        static public function getAllSocietyMembers($society_id)
        {
            $user_ids = App\UserSociety::where('society_id', $society_id)->get();
            $users = array();
            foreach ($user_ids as $user_id)
            {
                $id = $user_id->user_id;
                $user = App\User::where('id', $id)->first();
                array_push($users, $user);
            }
            return $users;
        }

    }