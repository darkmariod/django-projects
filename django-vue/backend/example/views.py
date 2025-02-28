from rest_framework.views import APIView
from django.http import HttpResponse

class Class_Example(APIView):

    def get(self, request):
        return HttpResponse("hola \")
