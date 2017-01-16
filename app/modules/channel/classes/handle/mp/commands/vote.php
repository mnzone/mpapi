<?php
/**
 * Created by PhpStorm.
 * User: Ray
 * Date: 2017/1/16
 * Time: 下午11:20
 */

namespace channel\handle\mp\commands;


use channel\handle\mp\Text;


class Vote extends Text
{

    private $account = false;
    private $voter = false;
    private $market = false;
    private $candidates = false;
    private $data = false;
    private $result = [
        'msg_type' => 'text',
        'context' => ''
    ];

    function __construct($account_id, $data)
    {
        $this->data = $data;
        $this->account = \Cache::get("wx_account_{$account_id}");
        $this->candidates = \Cache::get("candidates{$account_id}");
    }

    /**
     * 判断投票编号是否存在
     *
     * @param string $no
     */
    private function get_candidate($no = ''){
        $item = false;

        foreach ($this->candidates as $key => $candidate){
            if($candidate->no == $no
                || $candidate->keyword == $no){
                $item = $key;
                break;
            }
        }

        return $item;
    }

    private function voter_sum(){

        //获取参与情况
        $this->voter = false;
        try {
            $this->voter = \Cache::get("{$this->data->FromUserName}{$this->market->id}");
        } catch (\CacheNotFoundException $e) {
            $this->voter = \Model_MarketingRecordStatistic::forge([
                'openid' => $this->data->FromUserName,
                'marketing_id' => $this->market->id
            ]);
        }

        //检查参与数量
        if($this->voter && $this->market->limit){
            if($this->market->limit->involved_total_num && $this->market->limit->involved_total_num <= $this->voter->total_num){
                $this->result['context'] = "您最多只能投{$this->market->limit->involved_total_num}次票，回复“查询+编号”如“查询209”,查询其他选手成绩。";
                return false;
            }
        }

        //保存参与次数统计
        $this->voter->day_num += 1;
        $this->voter->total_num += 0;
        \Cache::set("{$this->data->FromUserName}{$this->market->id}", $this->voter, 365);
        return true;
    }

    public function handle(){
        $item = false;

        //判断被投票人是否存在
        $key = $this->get_candidate($this->data->Content);

        if( ! $key){
            //抱歉，该编号的选手不存在，回复“查询+编号”如“查询209”,查询其他选手成绩。
            $this->result['context'] = isset($this->account->vote_not_no) && $this->account->vote_not_no ? $this->account->vote_not_no : '抱歉，该编号的选手不存在。';
            return $this->result;
        }

        $candidate = $this->candidates[$key];
        $this->market = \Cache::get("marketing_{$candidate->marketing_id}");
        if($this->market->start_at > time() || $this->market->end_at < time()){
            $this->result['context'] = isset($this->account->vote_not_date_span) && $this->account->vote_not_date_span ?$this->account->vote_not_date_span : '抱歉，该编号的选手不在开放时间段!';
        }

        if( ! $this->voter_sum()){
            return $this->result;
        }

        //记录被投票项数量
        $candidate->total_gain += 1;
        $this->candidates[$key] = $item;
        \Cache::set($key, $this->candidates, 365);

        $this->result['context'] = "投票成功，{$candidate->no}号选手{$candidate->title}票数加1，总票数为{$candidate->total_gain}票!\n\n回复“查询+编号”如“查询209”,查询其他选手成绩。";
        return $this->result;
    }
}