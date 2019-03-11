package ders.yasin.com.jsoupapp;

import android.app.ProgressDialog;
import android.os.AsyncTask;
import android.support.v4.widget.CircularProgressDrawable;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.ProgressBar;

import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;

import java.io.IOException;
import java.util.ArrayList;

public class MainActivity extends AppCompatActivity {
    ListView lvVeriler;
    String URL="http://gerzemyo.sinop.edu.tr/?anaBirimNo=2&sayfaDil=28";
    ArrayList<String> veriList;
    ArrayAdapter<String> arrayAdapter;



    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        lvVeriler=findViewById(R.id.lv_Veriler);
        veriList=new ArrayList<String>();
        arrayAdapter=new ArrayAdapter<>(getApplicationContext(),android.R.layout.simple_list_item_1,veriList);

        new GetData().execute(URL);

    }

    public class GetData extends AsyncTask<String,Void,Document> {
        ProgressDialog progressDialog;
        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialog=new ProgressDialog(MainActivity.this);
            progressDialog.setTitle("Gerze MYO");
            progressDialog.setMessage("Loading");
            progressDialog.show();
        }
        @Override
        protected Document doInBackground(String... params) {
            Document doc;
            try {
                doc=Jsoup.connect(params[0]).get();
            } catch (IOException e) {
                e.printStackTrace();
                doc=null;
                Log.d("Jsoup Baglanti Hatasi",e.getMessage());
            }
            return doc;
        }
        @Override
        protected void onPostExecute(Document document) {
            super.onPostExecute(document);
            progressDialog.dismiss();
            /*Elements elements=document.select("td.haberResim img");
            for(int i=0;i<elements.size();i++){
                veriList.add(elements.get(i).attr("src"));
            }*/
            /*Elements elements=document.getElementsByTag("td");
            for(Element e:elements){
                if(e.className().equals("haberResim")){
                    Elements images=e.getElementsByTag("img");
                    for(Element image:images ){
                        veriList.add(image.attr("src"));
                    }
                }
            }*/

            <div id="duyuru">
            <ul>
                <li><a href="detay.aspx?birimNo=2&amp;dheNo=804">Yemek Bursu Başvuru Sonuçları</a></li>
                <li><a href="detay.aspx?birimNo=2&amp;dheNo=749">Ders Dönemi Staj Başvuruları</a></li>
                <li><a href="detay.aspx?birimNo=2&amp;dheNo=215">Sosyal Medya Hesaplarımız</a></li>
            </ul>
            </div>

            /*Elements elements=document.select("td.haberDetay");
            for(Element e:elements){
                veriList.add(e.text());
            }*/
            /*Elements elements=document.select("td.haberDetay a");
            for(Element e:elements){
                veriList.add(e.text());
            }*/

            Element altbaslik=document.getElementById("altIc");
            Elements tables=altbaslik.children();
            for(Element table:tables){
                Elements satirlar=table.children();
                for(Element satir:satirlar){
                    Elements sutunlar=satir.children();
                    for(Element sutun:sutunlar){
                        Elements divs=sutun.getElementsByTag("div");
                        for(Element div:divs){
                            Elements h3s=div.getElementsByTag("h3");
                            String baslik=h3s.get(0).text();
                                veriList.add(baslik);

                        }
                    }
                }
            }

            lvVeriler.setAdapter(arrayAdapter);

        }


    }
}
