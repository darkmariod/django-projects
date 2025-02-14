from django.shortcuts import render
from django.http import HttpResponse

def home_inicio(request):
    return HttpResponse("<h1>Hola</h1>")