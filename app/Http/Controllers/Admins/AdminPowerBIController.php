<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;

class AdminPowerBIController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function sales_dashboard_index(){
        $link = '';

        $user_id = Auth::id();

        if (session('role_id') == 1 || in_array($user_id, [12,70,57,69,32,8,2,36])) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiNWU5YjkwNjQtZmIzZC00NDE2LWE1YzItYWY3Mzk4NjdlNjUxIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if($user_id == 66){
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiMmEwNTFjYWQtNjY3ZS00MzIzLWJlNzgtNDZkY2MzZjIyZDBkIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';

        }
        else if($user_id == 109){
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiNjUzNzg5YjYtNGIyZS00N2FhLTgzNDItMjcxOWVkNDg2MTFhIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if($user_id == 157){
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiNTVjMTRmYTEtNzg0Ny00ZGI4LTljYjEtZjFlZTJiMDY1YjcwIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if($user_id == 13){
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiODM3YmVjYjYtOGFiMC00ZWQwLThlZGQtNDE2NTQzNzk3MDg5IiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if($user_id == 151){
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiMDMwYTM4OTAtNGViNC00YzBmLWIxZTQtZTIyYjc3ZGQxYjMyIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if($user_id == 197){
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiY2ZkNTUzNDAtYThhNy00NWM1LWJiZDYtZGFhODljMDJjNWFiIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if($user_id == 249){
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiMWNlMTIyYTgtZmI0ZS00ZTA5LThlMTQtOTZiYmIyY2VkOWQxIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if($user_id == 43){
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiMDMxZGUzZjMtYTc3Yi00ZDhlLTg1OTEtNWZjMjBkYzJmNTE0IiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if($user_id == 274){
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiOWM1MzA0NjQtNGIzYy00ZDQ5LTkyOTUtYzY4ZDY2MWQxNWU0IiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if($user_id == 90){
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiNmUxZmNiYzktM2JlMy00ZjJmLThhMzktZjU5MTNiMjc5NGI4IiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if($user_id == 232){
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiODRhNTVmODQtZThkZi00ODYxLWI2YjQtN2ZjNGZiMGRhNzNhIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if($user_id == 11){
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiYWVlYzhjZjAtYTFhYi00MDI2LTgwNjYtNWNlZWRiMDIwNjFjIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }

        return view('admin.reports.power_bi_sales_dashboard')->with(['link' => $link]);
    }

    public function operation_dashboard_index(){
        $link = '';

        $user_id = Auth::id();

        if (session('role_id') == 1 || in_array($user_id, [12,70,57,69,32,8,2,36])) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiZjdmOTk5OGItYTY3OC00MDI4LTg4YTUtNzkwYzRkZWNkMzdlIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if ($user_id == 161) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiODg4NTYxYWMtNzNkOC00MTQxLWE2ZmUtZWM4YTgwMDBkYzUyIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if ($user_id == 228) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiNzc3ODU2MjAtZTU5ZS00OTYxLThmOTQtMTQxMGFkMDMzMTViIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if ($user_id == 15) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiMzYxOWM2MWQtZTkyYy00YTUwLTg4MzItZWFmNTU3ZTdlODNkIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if ($user_id == 67) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiNDNkNjliOTctODEwMy00ODA3LTkxMTItMjE0MDEyNDY3ZWY1IiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if ($user_id == 21) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiMzkyMzI0MzgtMDE0Zi00NThlLWI4YTYtMzYwNDU4OTM4NGVjIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if ($user_id == 137) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiZDg1MmE1NGEtMzY5Ni00MmY5LWI3Y2UtZWMzMmE2MTc2NzFiIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if ($user_id == 256) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiZjA5ODc2NWEtOTA3Yy00YTIxLWI2OGQtOGNiNzAyMDNjNjhhIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if ($user_id == 18) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiODMwYzRmODgtYzcxNS00NjdmLWE4ZDQtMmU5YWIyM2Y4NWYxIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if ($user_id == 78) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiMDYzMTk3ZWYtOGY4ZS00ZGI4LTlkNTAtZDdmZjdlOWFkNmIxIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if ($user_id == 250) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiNTYxM2E0NWUtMjJlYy00NGM1LTk5YjMtNjcxOTE5MTgzYzBhIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if ($user_id == 19) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiNmYyYmVhZjItODEzMS00YzVhLWI2ZGYtMzQ3ZmZlODY0NzkxIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if ($user_id == 28) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiNDIyZmYyOWYtZWE0Mi00Y2NkLWJmMmMtYmUzODg0NzBlMmZkIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if (in_array($user_id, [239,20])) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiOWZhYWIyN2EtZjg0Mi00YzI2LTk1NjMtYjRkODdjOGIwOGQzIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if ($user_id == 39) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiMDJkNThmYzUtMDA2ZC00OWEwLThjYjgtZWZiNTg1NDljMTEzIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if ($user_id == 50) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiMDEyMWFlMzUtMjM1MS00MzBlLTlmOTItNGQ3Nzg2ZDk1ZDc1IiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if (in_array($user_id, [277,276,263])) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiYWI0YjRjYWUtMzhmZC00MGM2LTk3NjktZDQyN2FhNWI3NDliIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if (in_array($user_id, [214,347])) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiNjI1MTk5ODAtNWUxMy00YTgyLThmNzAtZjg3MzNlNjZlMDBiIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if ($user_id == 208) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiM2EwMGQ5NTQtOWNlNC00ZjliLThmYmYtYWUwMTIyYzdkNmI4IiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }else if ($user_id == 22) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiZDkzYjMzY2YtODVlYS00ZGI4LWFhODYtMWJkMzZkOWVmN2JjIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if ($user_id == 30) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiZjllYzMxODYtMzliMS00M2NiLWI3ODktZjk3N2NmNjljYjNkIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if ($user_id == 29) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiOWZhZmI2OTgtYWI5ZS00Yzk5LThkNzAtMDRhYmMwM2MxYzJiIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if (in_array($user_id, [33,126])) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiYjAyZTM1MjMtNzJkNC00NjVlLThhYzYtYmE2YzFkODI0NmQ0IiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }

        return view('admin.reports.power_bi_operation_dashboard')->with(['link' => $link]);
    }
}
