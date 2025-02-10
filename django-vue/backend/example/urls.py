from django.urls import path
from .views import *

urlpatterns = [
    path('/ejemplo', Class_Example.as_view())
]
