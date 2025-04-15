#include "productwidget.h"
#include "ui_productwidget.h"

productwidget::productwidget(QWidget *parent) :
    QDialog(parent),
    ui(new Ui::productwidget)
{
    ui->setupUi(this);
}

productwidget::~productwidget()
{
    delete ui;
}
